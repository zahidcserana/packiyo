<?php

namespace App\Components;

use App\Exceptions\ShippingException;
use App\Http\Resources\ShipmentResource;
use App\Jobs\Order\RecalculateReadyToShipOrders;
use App\Models\Order;
use App\Models\PrintJob;
use App\Models\Shipment;
use App\Models\ShipmentLabel;
use App\Models\Tote;
use App\Models\Webhook;
use Illuminate\Support\Arr;

class PackingComponent extends BaseComponent
{
    /**
     * @param Order $order
     * @param $storeRequest
     * @return mixed
     * @throws ShippingException
     */
    public function packAndShip(Order $order, $storeRequest)
    {
        if ($order->fulfilled_at || $order->cancelled_at) {
            return null;
        }

        $input = $storeRequest->all();

        if (Arr::exists($input, 'shipping_contact_information')) {
            $order->shippingContactInformation->update(
                Arr::get($input, 'shipping_contact_information')
            );
        }

        $shipment = app(ShippingComponent::class)->ship($order, $storeRequest);

        if ($shipment) {
            $printerId = Arr::get($input, 'printer_id');

            if ($printerId) {
                foreach ($shipment->shipmentLabels as $shipmentLabel) {
                    PrintJob::create([
                        'object_type' => ShipmentLabel::class,
                        'object_id' => $shipmentLabel->id,
                        'url' => route('shipment.label', [
                            'shipment' => $shipment,
                            'shipmentLabel' => $shipmentLabel,
                        ]),
                        'type' => $shipmentLabel->document_type,
                        'printer_id' => $printerId,
                        'user_id' => auth()->user()->id,
                    ]);
                }
            }

            $printPackingSlip = Arr::get($input, 'print_packing_slip');

            if ($printPackingSlip) {
                $defaultSlipPrinter = app('printer')->getDefaultSlipPrinter($order->customer);

                if ($defaultSlipPrinter) {
                    PrintJob::create([
                        'object_type' => Shipment::class,
                        'object_id' => $shipment->id,
                        'url' => route('shipment.getPackingSlip', [
                            'shipment' => $shipment,
                        ]),
                        'printer_id' => $defaultSlipPrinter->id,
                        'user_id' => auth()->user()->id
                    ]);

                    if ($order->custom_invoice_url) {
                        PrintJob::create([
                            'object_type' => Shipment::class,
                            'object_id' => $shipment->id,
                            'url' => $order->custom_invoice_url,
                            'printer_id' => $defaultSlipPrinter->id,
                            'user_id' => auth()->user()->id
                        ]);
                    }
                }
            }

            $this->webhook((new ShipmentResource($shipment))->toArray(request()), Shipment::class, Webhook::OPERATION_TYPE_STORE, $shipment->order->customer_id);

            $order->updateQuietly([
                'ready_to_ship' => 0,
                'ready_to_pick' => 0
            ]);

            dispatch(new RecalculateReadyToShipOrders([$order->id]));
        }

        return $shipment;
    }

    /**
     * @param $barcode
     * @return mixed
     */
    public function barcodeSearch($barcode): mixed
    {
        $customerIds = app('user')->getSelectedCustomers()->pluck('id')->toArray();

        $order = Order::whereIn('customer_id', $customerIds)
            ->where('number', ltrim($barcode))
            ->where('ready_to_pick', 1)
            ->first();

        if (!$order) {
            $tote = Tote::with('placedToteOrderItems.orderItem.order')
                ->join('warehouses', 'totes.warehouse_id', '=', 'warehouses.id')
                ->whereIn('warehouses.customer_id', $customerIds)
                ->where('barcode', $barcode)
                ->select('totes.*')
                ->first();

            if ($tote && !empty($tote->placedToteOrderItems)) {
                $order = $tote->placedToteOrderItems->first()->orderItem->order ?? null;
            }
        }

        if ($order) {
            return $order;
        }

        return null;
    }
}
