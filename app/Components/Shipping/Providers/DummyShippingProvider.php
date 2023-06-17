<?php

namespace App\Components\Shipping\Providers;

use App\Components\ReturnComponent;
use App\Http\Requests\Packing\PackageItemRequest;
use App\Http\Requests\Shipment\ShipItemRequest;
use App\Interfaces\BaseShippingProvider;
use App\Interfaces\ShippingProviderCredential;
use App\Models\CustomerSetting;
use App\Models\Lot;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Package;
use App\Models\PackageOrderItem;
use App\Models\Return_;
use App\Models\ReturnLabel;
use App\Models\Shipment;
use App\Models\ShipmentLabel;
use App\Models\ShippingCarrier;
use Barryvdh\DomPDF\Facade\Pdf;
use Carbon\Carbon;
use Illuminate\Support\Arr;
use Picqer\Barcode\BarcodeGeneratorPNG;

class DummyShippingProvider implements BaseShippingProvider
{
    public function getCarriers(ShippingProviderCredential $credential = null)
    {
        return true;
    }

    public function ship(Order $order, $storeRequest): ?Shipment
    {
        // TODO Fix later
        $input = $storeRequest->all();

        $orderItemsToShip = [];
        $packageItemRequests = [];

        // TODO: rewrite, make it more simple
        foreach ($input['order_items'] as $record) {
            $shipItemRequest = ShipItemRequest::make($record);
            $orderItem = OrderItem::find($record['order_item_id']);
            $orderItemsToShip[] = ['orderItem' => $orderItem, 'shipRequest' => $shipItemRequest];
        }

        $packingState = json_decode($input['packing_state'], true);

        // TODO: rewrite, make it more simple
        foreach ($packingState as $packingStateItem) {
            $packageItemRequest = PackageItemRequest::make($packingStateItem);
            $packageItemRequests[] = $packageItemRequest;
        }

        $shipment = new Shipment();
        $shipment->user_id = auth()->user()->id ?? 1;
        $shipment->order_id = $order->id;
        $shipment->shipping_method_id = null;
        $shipment->processing_status = Shipment::PROCESSING_STATUS_SUCCESS;
        $shipment->external_shipment_id = null;

        $shipment->save();

        app('shipment')->createContactInformation($order->shippingContactInformation->toArray(), $shipment);

        foreach ($orderItemsToShip as $orderItemToShip) {
            app('shipment')->shipItem($orderItemToShip['shipRequest'], $orderItemToShip['orderItem'], $shipment);
        }

        foreach ($packageItemRequests as $packageItemRequest) {
            $package = Package::create([
                'order_id' => $order->id,
                'shipping_box_id' => $packageItemRequest->box,
                'weight' => $packageItemRequest->weight,
                'length' => $packageItemRequest->_length,
                'width' => $packageItemRequest->width,
                'height' => $packageItemRequest->height,
                'shipment_id' => $shipment->id,
            ]);

            $packageOrderItems = [];

            foreach ($packageItemRequest->items as $packItem) {
                $orderItemId = Arr::get($packItem, 'orderItem');
                $packItemLocation = Arr::get($packItem, 'location');
                $packItemTote = Arr::get($packItem, 'tote');
                $serialNumber = Arr::get($packItem, 'serialNumber');

                $packageToItemKey = $orderItemId . '_' . $packItemLocation . '_' . $packItemTote . '_' . $serialNumber;

                $lot = Lot::select('lots.id')
                    ->join('lot_items', 'lot_items.lot_id', '=', 'lots.id')
                    ->join('products', 'lots.product_id', '=', 'products.id')
                    ->join('order_items', 'order_items.product_id', '=', 'products.id')
                    ->where('order_items.id', '=', $orderItemId)
                    ->where('lot_items.location_id', '=', $packItemLocation)
                    ->first();

                $lotId = $lot->id ?? null;

                if (isset($packageOrderItems[$packageToItemKey])) {
                    $packageOrderItems[$packageToItemKey]['quantity']++;
                } else {
                    $packageOrderItems[$packageToItemKey] = [
                        'order_item_id' => $orderItemId,
                        'package_id' => $package->id,
                        'location_id' => $packItemLocation,
                        'tote_id' => !empty($packItemTote) ? $packItemTote : null,
                        'serial_number' => $serialNumber,
                        'quantity' => 1,
                        'lot_id' => $lotId
                    ];
                }
            }

            foreach ($packageOrderItems as $packageOrderItem){
                PackageOrderItem::create($packageOrderItem);
            }
        }

        $this->storeShipmentLabelAndTracking($shipment);

        return $shipment;
    }

    public function return(Order $order, $storeRequest): Return_
    {
        $input = $storeRequest->all();
        $input['number'] = Return_::getUniqueIdentifier(ReturnComponent::NUMBER_PREFIX, $input['warehouse_id']);

        if (Arr::exists($input, 'shipping_contact_information')) {
            $order->shippingContactInformation->update(Arr::get($input, 'shipping_contact_information'));
        }

        if (isset($input['return_status_id']) && $input['return_status_id'] === 'pending') {
            Arr::forget($input, 'return_status_id');
        }

        $return = Return_::create(Arr::except($input, ['order_items']));

        if (isset($input['order_items'])) {
            app('return')->updateReturnItems($return, $input['order_items']);
        }

        if ($input['own_label'] === '0') {
            $this->storeOnlyReturnLabelAndTracking($return);
        }

        return $return;
    }

    private function storeOnlyReturnLabelAndTracking($return)
    {
        ReturnLabel::create([
            'return_id' => $return->id,
            'size' => '',
            'content' => base64_encode($this->generateReturnLabel($return)),
            'type' => 'pdf'
        ]);
    }

    private function storeShipmentLabelAndTracking(Shipment $shipment)
    {
        foreach ($shipment->packages as $package) {
            ShipmentLabel::create([
                'shipment_id' => $shipment->id,
                'size' => '',
                'content' => base64_encode($this->generateLabel($package)),
                'document_type' => 'pdf',
                'type' => ShipmentLabel::TYPE_SHIPPING
            ]);

            if (customer_settings($shipment->order->customer_id, CustomerSetting::CUSTOMER_SETTING_AUTO_RETURN_LABEL) === '1') {
                ShipmentLabel::create([
                    'shipment_id' => $shipment->id,
                    'size' => '',
                    'content' => base64_encode($this->generateLabel($package, ShipmentLabel::TYPE_RETURN)),
                    'document_type' => 'pdf',
                    'type' => ShipmentLabel::TYPE_RETURN
                ]);
            }
        }
    }

    private function generateLabel(Package $package, string $type = ShipmentLabel::TYPE_SHIPPING) {
        $generator = new BarcodeGeneratorPNG();

        $data = [
            'senderCustomerContactInformation' => $package->shipment->order->customer->contactInformation,
            'senderContactInformation' => $package->packageOrderItems->first()->location->warehouse->contactInformation,
            'receiverCustomerContactInformation' => $package->shipment->contactInformation,
            'receiverContactInformation' => $package->shipment->contactInformation,
            'barcode' => $generator->getBarcode($package->shipment->order->number, $generator::TYPE_CODE_128),
            'barcodeNumber' => $package->shipment->order->number
        ];        

        $paperWidth = dimension_width($package->shipment->order->customer, 'label');
        $paperHeight = dimension_height($package->shipment->order->customer, 'label');

        if ($type === ShipmentLabel::TYPE_RETURN) {
            $senderCustomerContactInformation = $data['senderCustomerContactInformation'];
            $senderContactInformation = $data['senderContactInformation'];
            $receiverCustomerContactInformation = $data['receiverCustomerContactInformation'];
            $receiverContactInformation = $data['receiverContactInformation'];

            $data['senderCustomerContactInformation'] = $receiverCustomerContactInformation;
            $data['senderContactInformation'] = $receiverContactInformation;
            $data['receiverCustomerContactInformation'] = $senderCustomerContactInformation;
            $data['receiverContactInformation'] = $senderContactInformation;

            return PDF::loadView('pdf.dummylabel', $data)
                ->setPaper([0, 0, $paperWidth, $paperHeight])
                ->output();
        }

        return PDF::loadView('pdf.dummylabel', $data)
            ->setPaper([0, 0, $paperWidth, $paperHeight])
            ->output();
    }

    public function void(Shipment $shipment): array
    {
        $shipment->voided_at = Carbon::now();

        $shipment->saveQuietly();

        return ['success' => true, 'message' => __('Shipment successfully voided.')];
    }

    private function generateReturnLabel(Return_ $return) {
        $generator = new BarcodeGeneratorPNG();

        $order = $return->order;

        $data = [
            'senderCustomerContactInformation' => $return->order->shippingContactInformation,
            'senderContactInformation' => $return->order->shippingContactInformation,
            'receiverCustomerContactInformation' => $return->order->customer->contactInformation,
            'receiverContactInformation' => $return->order->customer->warehouses->first()->contactInformation,
            'barcode' => $generator->getBarcode($return->id, $generator::TYPE_CODE_128),
            'barcodeNumber' => $return->id,
            'type' => 'Return',
        ];

        $labelWidth = customer_settings($order->customer_id, CustomerSetting::CUSTOMER_SETTING_LABEL_SIZE_WIDTH);

        if (!$labelWidth) {
            $labelWidth = 102;
        }

        $labelHeight = customer_settings($order->customer_id, CustomerSetting::CUSTOMER_SETTING_LABEL_SIZE_HEIGHT);

        if (!$labelHeight) {
            $labelHeight = 192;
        }

        return PDF::loadView('pdf.dummylabel', $data)
            ->setPaper([0, 0, $labelWidth * 2.83464567, $labelHeight * 2.83464567])
            ->output();
    }

    public function manifest(ShippingCarrier $shippingCarrier)
    {
        // TODO: Implement manifest() method.
    }
}
