<?php

namespace App\Components\Shipping\Providers;

use App\Components\ShippingComponent;
use App\Exceptions\ShippingException;
use App\Http\Requests\FormRequest;
use App\Http\Requests\Packing\BulkShipStoreRequest;
use App\Http\Requests\Packing\PackageItemRequest;
use App\Http\Requests\Shipment\ShipItemRequest;
use App\Interfaces\BaseShippingProvider;
use App\Interfaces\ShippingProviderCredential;
use App\Models\Currency;
use App\Models\Customer;
use App\Models\CustomerSetting;
use App\Models\EasypostCredential;
use App\Models\Lot;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Package;
use App\Models\PackageOrderItem;
use App\Models\Return_;
use App\Models\ReturnLabel;
use App\Models\ReturnTracking;
use App\Models\Shipment;
use App\Models\ShipmentLabel;
use App\Models\ShipmentTracking;
use App\Models\ShippingBox;
use App\Models\ShippingCarrier;
use App\Models\ShippingMethod;
use Carbon\Carbon;
use Exception;
use GuzzleHttp\Client;
use GuzzleHttp\Exception\RequestException;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Log;
use setasign\Fpdi\PdfReader\PageBoundaries;
use setasign\Fpdi\Tcpdf\Fpdi;

class EasypostShippingProvider implements BaseShippingProvider
{
    public const BASE_URL = 'https://api.easypost.com/v2';

    private const CARRIER_PREDEFINED_PACKAGES = [
        'FedEx' => [
            'FedExEnvelope',
            'FedExBox',
            'FedExPak',
            'FedExTube',
            'FedEx10kgBox',
            'FedEx25kgBox',
            'FedExSmallBox',
            'FedExMediumBox',
            'FedExLargeBox',
            'FedExExtraLargeBox'
        ]
    ];

    /**
     * @param EasypostCredential|null $credential
     * @return void
     */
    public function getCarriers(ShippingProviderCredential $credential = null)
    {
        $carrierService = array_search(get_class($this), ShippingComponent::SHIPPING_CARRIERS);
        $credentials = new Collection();

        if ($credential != null) {
            $credentials->add($credential);
        } else {
            $credentials = EasypostCredential::all();
        }

        foreach ($credentials as $credential) {
            $customerIds = [$credential->customer_id];

            $customer = $credential->customer;

            if ($customer->children) {
                $customerIds = array_merge($customerIds, $customer->children->pluck('id')->toArray());
            }

            $orders = Order::whereIn('customer_id', $customerIds)
                ->whereNull('cancelled_at')
                ->whereNull('fulfilled_at')
                ->inRandomOrder()
                ->limit(50)
                ->get();

            foreach ($orders as $order) {
                Log::info('[Easypost Carrier] Order ID ', [$order->id]);

                $shipmentRequestBody = $this->createShipmentRequestForCarrierListBody($order, $credential);

                try {
                    $response = $this->send(
                        $credential,
                        'POST',
                        '/shipments',
                        $shipmentRequestBody
                    );

                    if ($response && !empty($response['rates']) && count($response['rates']) > 0) {
                        foreach ($response['rates'] as $shippingRate) {
                            $externalCarrierId = Arr::get($shippingRate, 'carrier_account_id');

                            $shippingCarrier = ShippingCarrier::withTrashed()
                                ->where('customer_id', $customer->id)
                                ->where('carrier_service', $carrierService)
                                ->whereJsonContains('settings', ['external_carrier_id' => $externalCarrierId])
                                ->first();

                            if (!$shippingCarrier) {
                                $shippingCarrier = ShippingCarrier::create([
                                    'customer_id' => $customer->id,
                                    'carrier_service' => $carrierService,
                                    'settings' => [
                                        'external_carrier_id' => $externalCarrierId
                                    ]
                                ]);

                                $shippingCarrier->credential()->associate($credential);
                            }

                            $shippingCarrier->name = Arr::get($shippingRate, 'carrier');

                            $shippingCarrier->save();
                            $shippingCarrier->restore();

                            $shippingMethod = ShippingMethod::withTrashed()->firstOrCreate([
                                'shipping_carrier_id' => $shippingCarrier->id,
                                'name' => Arr::get($shippingRate, 'service')
                            ]);

                            $shippingMethod->restore();
                        }
                    }
                } catch (Exception $exception) {

                }
            }
        }
    }

    public function createShipmentRequestForCarrierListBody(Order $order, EasypostCredential $credential)
    {
        $request['reference'] = $order->number;

        $customerAddress = $order->customer->contactInformation;
        $warehouse = $order->customer->parent_id ? $order->customer->parent->warehouses->first() : $order->customer->warehouses->first();
        $customerWarehouseAddress = $warehouse->contactInformation;

        $contactInformationData = $order->shippingContactInformation->toArray();

        $country = $order->shippingContactInformation->country->iso_3166_2 ?? null;

        $deliveryAddress['name'] = $contactInformationData['name'] ?? null;
        $deliveryAddress['street1'] = $contactInformationData['address'];
        $deliveryAddress['street2'] = $contactInformationData['address2'];
        $deliveryAddress['city'] = $contactInformationData['city'];
        $deliveryAddress['state'] = $contactInformationData['state'];
        $deliveryAddress['zip'] = $contactInformationData['zip'];
        $deliveryAddress['country'] = $country;
        $deliveryAddress['phone'] = $contactInformationData['phone'];
        $deliveryAddress['email'] = $contactInformationData['email'];

        if ($country == 'US') {
            $deliveryAddress['verify'] = true;
        }

        $request['shipment']['to_address'] = $deliveryAddress;

        $senderAddress['name'] = $customerAddress->name;
        $senderAddress['street1'] = $customerWarehouseAddress->address;
        $senderAddress['street2'] = $customerWarehouseAddress->address2;
        $senderAddress['city'] = $customerWarehouseAddress->city;
        $senderAddress['state'] = $customerWarehouseAddress->state;
        $senderAddress['zip'] = $customerWarehouseAddress->zip;
        $senderAddress['country'] = $customerWarehouseAddress->country->iso_3166_2 ?? null;
        $senderAddress['phone'] = $customerWarehouseAddress->phone;
        $senderAddress['email'] = $customerWarehouseAddress->email;

        $request['shipment']['from_address'] = $senderAddress;

        $customsItems = [];

        $shippingBoxId = customer_settings($order->customer->id, CustomerSetting::CUSTOMER_SETTING_SHIPPING_BOX_ID);

        $length = 0.001;
        $width = 0.001;
        $height = 0.001;

        if ($shippingBoxId) {
            $shippingBox = ShippingBox::find($shippingBoxId);
        } else {
            $shippingBoxes = $order->customer->shippingBoxes;

            if ($order->customer->parent_id) {
                $shippingBoxes = $shippingBoxes->merge($order->customer->parent->shippingBoxes);
            }

            $shippingBox = $shippingBoxes->first();
        }

        if ($shippingBox) {
            $length = $shippingBox->length;
            $width = $shippingBox->width;
            $height = $shippingBox->height;
        }

        $weight = 0;

        if ($order->currency) {
            $currency = $order->currency->code;
        } else {
            $customerCurrency = Currency::find(customer_settings($order->customer->id, CustomerSetting::CUSTOMER_SETTING_CURRENCY));

            if ($customerCurrency) {
                $currency = $customerCurrency->code;
            }
        }

        foreach ($order->orderItems as $orderItem) {
            if ($orderItem->quantity > 0) {
                $orderItemWeightInOz = max($this->getWeightInOz($order->customer, $orderItem->weight), 0.01) * $orderItem->quantity;

                $description = $orderItem->product->customs_description;

                if (empty($description)) {
                    $description = $orderItem->name;
                }

                $customsItems[] = [
                    'description' => mb_substr($description, 0, 50),
                    'quantity' => $orderItem->quantity,
                    'value' => max(2, $orderItem->price),
                    'weight' => (string) $orderItemWeightInOz,
                    'hs_tariff_number' => $orderItem->product->hs_code,
                    'code' => mb_substr($orderItem->sku, 0, 20),
                    'origin_country' => $orderItem->product->country->iso_3166_2 ?? 'US',
                    'currency' => $currency,
                    'shipping_cost' => 1,
                ];
                $weight += $orderItemWeightInOz;
            }
        }

        $parcel['length'] = (string) $length;
        $parcel['width'] = (string) $width;
        $parcel['height'] = (string) $height;
        $parcel['weight'] = (string) $weight;

        $customsInfo = [
            'customs_certify' => 'true',
            'customs_signer' => $credential->customs_signer,
            'contents_type' => $credential->contents_type,
            'contents_explanation' => $credential->contents_explanation,
            'restriction_type' => 'none',
            'eel_pfc' => $credential->eel_pfc,
            'customs_items' => $customsItems
        ];

        $request['shipment']['parcel'] = $parcel;
        $request['shipment']['customs_info'] = $customsInfo;

        $request['shipment']['options'] = [
            'label_size' => '4x6',
            'label_format' => 'pdf'
        ];

        return $request;
    }

    /**
     * @throws ShippingException
     */
    public function ship(Order $order, FormRequest $storeRequest): ?Shipment
    {
        $input = $storeRequest->all();

        $shippingRateId = $input['shipping_method_id'];
        $shippingMethod = ShippingMethod::find($shippingRateId);

        $orderItemsToShip = [];
        $packageItemRequests = [];

        foreach ($input['order_items'] as $record) {
            $shipItemRequest = ShipItemRequest::make($record);
            $orderItem = OrderItem::find($record['order_item_id']);
            $orderItemsToShip[] = ['orderItem' => $orderItem, 'shipRequest' => $shipItemRequest];
        }

        $packingState = json_decode($input['packing_state'], true);

        $responses = [];

        foreach ($packingState as $packingStateItem) {
            $packageItemRequest = PackageItemRequest::make($packingStateItem);
            $packageItemRequests[] = $packageItemRequest;

            $shipmentRequestBody = $this->createShipmentRequestBody($order, $storeRequest, $packageItemRequest, $shippingMethod);

            $response = $this->send(
                $shippingMethod->shippingCarrier->credential,
                'POST',
                '/shipments',
                $shipmentRequestBody
            );

            if ($response) {
                $responses[] = $response;
            }
        }

        if (!empty($responses)) {
            $externalShipmentId = $response['id'];

            $shipment = new Shipment();
            $shipment->user_id = auth()->user()->id ?? 1;
            $shipment->order_id = $order->id;
            $shipment->shipping_method_id = $shippingMethod->id;
            $shipment->processing_status = Shipment::PROCESSING_STATUS_SUCCESS;
            $shipment->external_shipment_id = $externalShipmentId;

            if (Arr::get($input, 'drop_point_id')) {
                $shipment->drop_point_id = Arr::get($input, 'drop_point_id');
            }

            $shipment->save();

            app('shipment')->createContactInformation($order->shippingContactInformation->toArray(), $shipment);

            $orderItemToQuantity = [];

            foreach ($orderItemsToShip as $orderItemToShip) {
                app('shipment')->shipItem($orderItemToShip['shipRequest'], $orderItemToShip['orderItem'], $shipment);
                $orderItemToQuantity[$orderItemToShip['orderItem']->id] = $orderItemToShip['orderItem']->quantity;
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
                    $orderItemId = $packItem['orderItem'];
                    $packItemLocation = $packItem['location'];
                    $packItemTote = $packItem['tote'];
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

                foreach ($packageOrderItems as $packageOrderItem) {
                    PackageOrderItem::create($packageOrderItem);
                }
            }

            $this->storeShipmentLabelAndTracking($shipment, $responses);

            if (customer_settings($shipment->order->customer_id, CustomerSetting::CUSTOMER_SETTING_AUTO_RETURN_LABEL) === '1') {
                $shippingMethod = app('shippingMethodMapping')->returnShippingMethod($order) ?? $shippingMethod;

                $this->createAutoReturnLabels($packingState, $order, $storeRequest, $shippingMethod, $shipment);
            }

            return $shipment;
        }

        return null;
    }

    public function return(Order $order, $storeRequest): ?Return_
    {
        $input = $storeRequest->all();

        $shippingRateId = $input['shipping_method_id'];
        $shippingMethod = ShippingMethod::find($shippingRateId);

        $packageItemRequests = [];
        $defaultBox = $order->customer->shippingBoxes->first();

        $orderItemsArr = [];
        $totalWeight = 0;

        foreach ($input['order_items'] as $record)
        {
            $orderItem = OrderItem::find($record['order_item_id']);
            $totalWeight += $orderItem->weight;
            $orderItemsArr[] = [
                'orderItem' => $record['order_item_id'],
                'serialNumber' => '',
                'packedParentKey' => ''
            ];
        }

        $packingStateItem = [
            'items' => $orderItemsArr,
            'weight' => $totalWeight,
            'box' => $defaultBox->id,
            '_length' => $defaultBox->length,
            'width' => $defaultBox->width,
            'height' => $defaultBox->height,
        ];

        $responses = [];

        $packageItemRequest = PackageItemRequest::make($packingStateItem);
        $packageItemRequests[] = $packageItemRequest;

        $requestBody = $this->createReturnRequestBody($order, $storeRequest, $packageItemRequest, $shippingMethod);

        $response = $this->send(
            $shippingMethod->shippingCarrier->credential,
            'POST',
            '/shipments',
            $requestBody
        );

        if ($response) {
            $responses[] = $response;
        }

        if (!empty($responses)) {

            if (Arr::exists($input, 'shipping_contact_information')) {
                $order->shippingContactInformation->update(Arr::get($input, 'shipping_contact_information'));
            }

            if (isset($input['return_status_id']) && $input['return_status_id'] === 'pending') {
                Arr::forget($input, 'return_status_id');
            }

            $return = Return_::create(Arr::except($input, ['order_items']));

            if (isset($input['order_items'])) {
                app()->return->updateReturnItems($return, $input['order_items']);
            }

            $this->storeReturnLabelAndTracking($return, $responses, $input['own_label']);

            return $return;
        }

        return null;
    }

    public function createShipmentRequestBody(Order $order, FormRequest $storeRequest, PackageItemRequest $packageItemRequest, ShippingMethod $shippingMethod)
    {
        $request['reference'] = $order->number;

        $packageItemInput = $packageItemRequest->all();

        $customerAddress = $order->customer->contactInformation;
        $warehouse = $order->customer->parent_id ? $order->customer->parent->warehouses->first() : $order->customer->warehouses->first();
        $customerWarehouseAddress = $warehouse->contactInformation;

        $contactInformationData = $order->shippingContactInformation->toArray();

        $country = $order->shippingContactInformation->country->iso_3166_2 ?? null;

        $deliveryAddress['name'] = $contactInformationData['name'] ?? null;
        $deliveryAddress['street1'] = $contactInformationData['address'];
        $deliveryAddress['street2'] = $contactInformationData['address2'];
        $deliveryAddress['city'] = $contactInformationData['city'];
        $deliveryAddress['state'] = $contactInformationData['state'];
        $deliveryAddress['zip'] = $contactInformationData['zip'];
        $deliveryAddress['country'] = $country;
        $deliveryAddress['phone'] = $contactInformationData['phone'];
        $deliveryAddress['email'] = $contactInformationData['email'];

        if ($country === 'US') {
            $deliveryAddress['verify'] = true;
        }

        $request['shipment']['to_address'] = $deliveryAddress;

        $senderAddress['name'] = $customerAddress->name;
        $senderAddress['street1'] = $customerWarehouseAddress->address;
        $senderAddress['street2'] = $customerWarehouseAddress->address2;
        $senderAddress['city'] = $customerWarehouseAddress->city;
        $senderAddress['state'] = $customerWarehouseAddress->state;
        $senderAddress['zip'] = $customerWarehouseAddress->zip;
        $senderAddress['country'] = $customerWarehouseAddress->country->iso_3166_2 ?? null;
        $senderAddress['phone'] = $customerWarehouseAddress->phone;
        $senderAddress['email'] = $customerWarehouseAddress->email;

        $request['shipment']['from_address'] = $senderAddress;

        $parcel['length'] = (string) max(0.01, $packageItemRequest->_length);
        $parcel['width'] = (string) max(0.01, $packageItemRequest->width);
        $parcel['height'] = (string) max(0.01, $packageItemRequest->height);
        $parcel['weight'] = (string) max(0.01, $this->getWeightInOz($order->customer, $packageItemRequest->weight));

        $shippingBox = ShippingBox::find($packageItemRequest->box);

        if ($shippingBox
            && isset(self::CARRIER_PREDEFINED_PACKAGES[$shippingMethod->shippingCarrier->name])
            && in_array($shippingBox->name, self::CARRIER_PREDEFINED_PACKAGES[$shippingMethod->shippingCarrier->name])
        ) {
            $parcel['predefined_package'] = $shippingBox->name;
        }

        $packageItems = [];

        foreach ($packageItemInput['items'] as $packageItem) {
            if (!isset($packageItems[$packageItem['orderItem']])) {
                $packageItems[$packageItem['orderItem']] = 0;
            }

            $packageItems[$packageItem['orderItem']]++;
        }

        $customsItems = [];

        if ($order->currency) {
            $currency = $order->currency->code;
        } else {
            $customerCurrency = Currency::find(customer_settings($order->customer->id, CustomerSetting::CUSTOMER_SETTING_CURRENCY));

            if ($customerCurrency) {
                $currency = $customerCurrency->code;
            }
        }

        foreach ($packageItems as $orderItemId => $quantity) {
            $orderItem = OrderItem::find($orderItemId);

            if ($orderItem) {
                $orderItemWeightInOz = max($this->getWeightInOz($order->customer, $orderItem->weight), 0.01) * $quantity;

                $description = $orderItem->product->customs_description;

                if (empty($description)) {
                    $description = $orderItem->name;
                }

                $customsItems[] = [
                    'description' => mb_substr($description, 0, 50),
                    'quantity' => $quantity,
                    'value' => max(2, $orderItem->price),
                    'weight' => (string) $orderItemWeightInOz,
                    'hs_tariff_number' => $orderItem->product->hs_code,
                    'code' => mb_substr($orderItem->sku, 0, 20),
                    'origin_country' => $orderItem->product->country->iso_3166_2 ?? 'US',
                    'currency' => $currency,
                    'shipping_cost' => 1,
                ];
            }
        }

        $customsInfo = [
            'customs_certify' => 'true',
            'customs_signer' => $shippingMethod->shippingCarrier->credential->customs_signer,
            'contents_type' => $shippingMethod->shippingCarrier->credential->contents_type,
            'contents_explanation' => $shippingMethod->shippingCarrier->credential->contents_explanation,
            'restriction_type' => 'none',
            'eel_pfc' => $shippingMethod->shippingCarrier->credential->eel_pfc,
            'customs_items' => $customsItems
        ];

        $request['shipment']['parcel'] = $parcel;
        $request['shipment']['customs_info'] = $customsInfo;

        $request['shipment']['service'] = $shippingMethod->name;
        $request['shipment']['carrier_accounts'] = [$shippingMethod->shippingCarrier->settings['external_carrier_id']];

        $labelFormat = customer_settings($order->customer_id, CustomerSetting::CUSTOMER_SETTING_USE_ZPL_LABELS) ? 'zpl' : 'pdf';

        // With bulk shipping, we are merging the labels together in one big PDF. We cannot merge ZPLs.
        if (get_class($storeRequest) == BulkShipStoreRequest::class) {
            $labelFormat = 'pdf';
        }

        $request['shipment']['options'] = [
            'label_size' => '4x6',
            'label_format' => $labelFormat,
//            'invoice_number' => 'USPS Approved CK-100'
        ];

        return $request;
    }

    public function createReturnRequestBody(Order $order, $storeRequest, PackageItemRequest $packageItemRequest, ShippingMethod $shippingMethod)
    {
        $packageItemInput = $packageItemRequest->all();

        $customerAddress = $order->customer->contactInformation;
        $warehouse = $order->customer->parent_id ? $order->customer->parent->warehouses->first() : $order->customer->warehouses->first();
        $customerWarehouseAddress = $warehouse->contactInformation;

        $contactInformationData = $order->shippingContactInformation->toArray();

        $country = $order->shippingContactInformation->country->iso_3166_2 ?? null;

        $deliveryAddress['name'] = $customerAddress->name;
        $deliveryAddress['street1'] = $customerWarehouseAddress->address;
        $deliveryAddress['street2'] = $customerWarehouseAddress->address2;
        $deliveryAddress['city'] = $customerWarehouseAddress->city;
        $deliveryAddress['state'] = $customerWarehouseAddress->state;
        $deliveryAddress['zip'] = $customerWarehouseAddress->zip;
        $deliveryAddress['country'] = $customerWarehouseAddress->country->iso_3166_2 ?? $customerAddress->country->iso_3166_2;
        $deliveryAddress['phone'] = $customerWarehouseAddress->phone;
        $deliveryAddress['email'] = $customerWarehouseAddress->email;

        if ($country == 'US') {
            $deliveryAddress['verify'] = true;
        }

        $request['shipment']['to_address'] = $deliveryAddress;

        $senderAddress['name'] = $contactInformationData['name'] ?? null;
        $senderAddress['street1'] = $contactInformationData['address'];
        $senderAddress['street2'] = $contactInformationData['address2'];
        $senderAddress['city'] = $contactInformationData['city'];
        $senderAddress['state'] = $contactInformationData['state'];
        $senderAddress['zip'] = $contactInformationData['zip'];
        $senderAddress['country'] = $country;
        $senderAddress['phone'] = $contactInformationData['phone'];
        $senderAddress['email'] = $contactInformationData['email'];

        $request['shipment']['from_address'] = $senderAddress;

        $parcel['length'] = (string) max(0.01, $packageItemRequest->_length);
        $parcel['width'] = (string) max(0.01, $packageItemRequest->width);
        $parcel['height'] = (string) max(0.01, $packageItemRequest->height);
        $parcel['weight'] = (string) max(0.01, $this->getWeightInOz($order->customer, $packageItemRequest->weight));

        $packageItems = [];

        foreach ($packageItemInput['items'] as $packageItem) {
            if (!isset($packageItems[$packageItem['orderItem']])) {
                $packageItems[$packageItem['orderItem']] = 0;
            }

            $packageItems[$packageItem['orderItem']]++;
        }

        $customsItems = [];

        if ($order->currency) {
            $currency = $order->currency->code;
        } else {
            $customerCurrency = Currency::find(customer_settings($order->customer->id, CustomerSetting::CUSTOMER_SETTING_CURRENCY));

            if ($customerCurrency) {
                $currency = $customerCurrency->code;
            }
        }

        foreach ($packageItems as $orderItemId => $quantity) {
            $orderItem = OrderItem::find($orderItemId);
            if ($orderItem) {
                $orderItemWeightInOz = max($this->getWeightInOz($order->customer, $orderItem->weight), 0.01) * $quantity;

                $description = $orderItem->product->customs_description;

                if (empty($description)) {
                    $description = $orderItem->name;
                }

                $customsItems[] = [
                    'description' => mb_substr($description, 0, 50),
                    'quantity' => $quantity,
                    'value' => max(0.01, $orderItem->price),
                    'weight' => (string) $orderItemWeightInOz,
                    'hs_tariff_number' => $orderItem->product->hs_code,
                    'code' => mb_substr($orderItem->sku, 0, 20),
                    'origin_country' => $orderItem->product->country->iso_3166_2 ?? 'US',
                    'currency' => $currency,
                    'shipping_cost' => 1,
                ];
            }
        }

        $customsInfo = [
            'customs_certify' => 'true',
            'customs_signer' => $shippingMethod->shippingCarrier->credential->customs_signer,
            'contents_type' => $shippingMethod->shippingCarrier->credential->contents_type,
            'contents_explanation' => '',
            'restriction_type' => 'none',
            'eel_pfc' => $shippingMethod->shippingCarrier->credential->eel_pfc,
            'customs_items' => $customsItems
        ];

        $request['shipment']['parcel'] = $parcel;
        $request['shipment']['customs_info'] = $customsInfo;

        $request['shipment']['service'] = $shippingMethod->name;
        $request['shipment']['carrier_accounts'] = [$shippingMethod->shippingCarrier->settings['external_carrier_id']];

        $labelFormat = customer_settings($order->customer_id, CustomerSetting::CUSTOMER_SETTING_USE_ZPL_LABELS) ? 'zpl' : 'pdf';

        // With bulk shipping, we are merging the labels together in one big PDF. We cannot merge ZPLs.
        if (get_class($storeRequest) == BulkShipStoreRequest::class) {
            $labelFormat = 'pdf';
        }

        $request['shipment']['options'] = [
            'label_size' => '4x6',
            'label_format' => $labelFormat
        ];

        return $request;
    }

    private function storeShipmentLabelAndTracking(Shipment $shipment, $carrierResponses)
    {
        foreach ($carrierResponses as $carrierResponse) {
            ShipmentLabel::create([
                'shipment_id' => $shipment->id,
                'size' => Arr::get($carrierResponse, 'postage_label.label_size'),
                'url' => Arr::get($carrierResponse, 'postage_label.label_url'),
                'document_type' => strtolower(Arr::get($carrierResponse, 'options.label_format')),
                'content' => base64_encode($this->getLabelContent($shipment, $carrierResponse)),
                'type' => ShipmentLabel::TYPE_SHIPPING
            ]);

            ShipmentTracking::create([
                'shipment_id' => $shipment->id,
                'tracking_number' => Arr::get($carrierResponse, 'tracker.tracking_code'),
                'tracking_url' => Arr::get($carrierResponse, 'tracker.public_url'),
                'type' => ShipmentTracking::TYPE_SHIPPING
            ]);
        }
    }

    private function storeReturnLabelAndTracking(Return_ $return, $carrierResponses, $ownLabel)
    {
        foreach ($carrierResponses as $carrierResponse) {
            if ($ownLabel === '0') {
                ReturnLabel::create([
                    'return_id' => $return->id,
                    'size' => Arr::get($carrierResponse, 'postage_label.label_size'),
                    'url' => Arr::get($carrierResponse, 'postage_label.label_url'),
                    'type' => strtolower(Arr::get($carrierResponse, 'options.label_format')),
                    'content' => base64_encode($this->getLabelContent($return, $carrierResponse))
                ]);
            }

            ReturnTracking::create([
                'return_id' => $return->id,
                'tracking_number' => Arr::get($carrierResponse, 'tracker.tracking_code'),
                'tracking_url' => Arr::get($carrierResponse, 'tracker.public_url')
            ]);
        }
    }

    private function send(EasypostCredential $easypostCredential, $method, $endpoint, $data = null, $returnException = true)
    {
        Log::info('[Easypost] send', [
            'easypost_credential_id' => $easypostCredential->id,
            'method' => $method,
            'endpoint' => $endpoint,
            'data' => $data,
        ]);

        $credentials = $this->getApiCredentials($easypostCredential);
        $url = self::BASE_URL . $endpoint;

        $client = new Client([
            'headers' => [
                'Content-Type' => 'application/json',
                'Authorization' => 'Basic ' . base64_encode(Arr::get($credentials, 'apiKey') . ':'),
            ],
        ]);

        try {
            Log::debug($url);
            $response = $client->request($method, $url, $method === 'GET' ? [] : ['body' => json_encode($data)]);
            $body = json_decode($response->getBody()->getContents() ?? null, true);

            Log::info('[Easypost] response', [$body]);

            return $body;
        } catch (RequestException $exception) {
            Log::error('[Easypost] exception thrown', [$exception->getResponse()->getBody()]);

            if ($returnException) {
                throw new ShippingException($exception->getResponse()->getBody());
            }
        }

        return null;
    }

    private function getApiCredentials(EasypostCredential $easypostCredential)
    {
        $apiKey = $easypostCredential->api_key ?? '';

        return compact('apiKey');
    }

    private function getWeightInOz(Customer $customer, $weight)
    {
        $weightUnit = customer_settings($customer->id, CustomerSetting::CUSTOMER_SETTING_WEIGHT_UNIT);

        if ($weightUnit == 'lb') {
            return $weight * 16;
        } else if ($weightUnit == 'oz') {
            return $weight;
        } else if ($weightUnit == 'kg') {
            return $weight * 35.274;
        } else if ($weightUnit == 'g') {
            return $weight * 0.035274;
        }

        return $weight;
    }

    /**
     * @param Model $object
     * @param $carrierResponse
     * @return string
     */
    private function getLabelContent($object, $carrierResponse): string
    {
        try {
            $carrierName = $object->shippingMethod->shippingCarrier->name;

            $labelWidth = dimension_width($object->order->customer, 'label');
            $labelHeight = dimension_height($object->order->customer, 'label');

            $tmpLabelPath = tempnam(sys_get_temp_dir(), 'label');
            file_put_contents($tmpLabelPath, file_get_contents(Arr::get($carrierResponse, 'postage_label.label_url')));

            $fpdi = new Fpdi('P', 'pt', [$labelWidth, $labelHeight]);
            $fpdi->setPrintHeader(false);
            $fpdi->setPrintFooter(false);

            $pageCount = $fpdi->setSourceFile($tmpLabelPath);

            for ($i = 1; $i <= $pageCount; $i++) {
                $fpdi->AddPage();
                $tplId = $fpdi->importPage($i, PageBoundaries::ART_BOX);

                if ($carrierName == 'DHLExpress') {
                    $size = $fpdi->getTemplateSize($tplId, null, $labelHeight);
                    $size['x'] = -10;
                } else if ($carrierName == 'FedEx') {
                    $size = $fpdi->getTemplateSize($tplId, $labelWidth * 2);
                    $size['x'] = -5;
                } else {
                    $size = $fpdi->getTemplateSize($tplId);
                }

                $fpdi->useTemplate($tplId, $size);

            }

            return $fpdi->Output('label.pdf', 'S');
        } catch (Exception $exception) {
            Log::error('[Easypost] getLabelContent', [$exception->getMessage()]);
            return '';
        }
    }

    public function void(Shipment $shipment): array
    {
        $response = $this->send(
            $shipment->shippingMethod->shippingCarrier->credential,
            'POST',
            '/shipments/' . $shipment->external_shipment_id . '/refund'
        );

        if (!empty($response)) {
            $shipment->voided_at = Carbon::now();

            $shipment->saveQuietly();

            return ['success' => true, 'message' => __('Shipment successfully voided.')];
        }

        return ['success' => false, 'message' => __('Something went wrong!')];
    }

    /**
     * @param mixed $packingState
     * @param Order $order
     * @param FormRequest $storeRequest
     * @param mixed $shippingMethod
     * @param Shipment $shipment
     * @return void
     * @throws ShippingException
     */
    private function createAutoReturnLabels(mixed $packingState, Order $order, FormRequest $storeRequest, mixed $shippingMethod, Shipment $shipment): void
    {
        foreach ($packingState as $packingStateItem) {
            $packageItemRequest = PackageItemRequest::make($packingStateItem);

            $shipmentRequestBody = $this->createShipmentRequestBody($order, $storeRequest, $packageItemRequest, $shippingMethod);

            $autoReturnLabelRequestBody = $this->createAutoReturnLabelRequestBody($shipmentRequestBody);

            $carrierResponse = $this->send(
                $shippingMethod->shippingCarrier->credential,
                'POST',
                '/shipments',
                $autoReturnLabelRequestBody,
                false
            );

            if ($carrierResponse) {
                ShipmentLabel::create([
                    'shipment_id' => $shipment->id,
                    'size' => Arr::get($carrierResponse, 'postage_label.label_size'),
                    'url' => Arr::get($carrierResponse, 'postage_label.label_url'),
                    'document_type' => strtolower(Arr::get($carrierResponse, 'options.label_format')),
                    'content' => base64_encode($this->getLabelContent($shipment, $carrierResponse)),
                    'type' => ShipmentLabel::TYPE_RETURN
                ]);

                ShipmentTracking::create([
                    'shipment_id' => $shipment->id,
                    'tracking_number' => Arr::get($carrierResponse, 'tracker.tracking_code'),
                    'tracking_url' => Arr::get($carrierResponse, 'tracker.public_url'),
                    'type' => ShipmentTracking::TYPE_RETURN
                ]);
            }
        }
    }

    /**
     * @param array $shipmentRequestBody
     * @return array
     */
    private function createAutoReturnLabelRequestBody(array $shipmentRequestBody): array
    {
        $deliveryAddress = $shipmentRequestBody['shipment']['to_address'];

        $senderAddress = $shipmentRequestBody['shipment']['from_address'];

        $shipmentRequestBody['shipment']['to_address'] = $senderAddress;

        $shipmentRequestBody['shipment']['from_address'] = $deliveryAddress;

        return $shipmentRequestBody;
    }

    public function manifest(ShippingCarrier $shippingCarrier)
    {
        $count = 100;

        if (config('app.env') == 'production') {
            $count = 500;

            if ($shippingCarrier->name != 'DhlEcs') {
                return;
            }
        }

        Shipment::whereIn('shipping_method_id', $shippingCarrier->shippingMethods->pluck('id'))
            ->whereNull('voided_at')
            ->whereNull('external_manifest_id')
            ->chunkById(/**
            * @param Shipment[] $shipments
            * @return void
            */ $count, function($shipments) use ($shippingCarrier) {
                $batchRequest = [
                    'batch' => [
                        'shipments' => []
                    ]
                ];

                foreach ($shipments as $shipment) {
                    $batchRequest['batch']['shipments'][] = [
                        'id' => $shipment->external_shipment_id
                    ];
                }

                $batchResponse = $this->send($shippingCarrier->credential,
                    'POST',
                    '/batches',
                    $batchRequest
                );

                if ($batchResponse && Arr::get($batchResponse, 'id')) {
                    Shipment::whereIntegerInRaw('id', $shipments->pluck('id')->toArray())->update([
                        'external_manifest_id' => Arr::get($batchResponse, 'id')
                    ]);
                }
            });
    }

    public function scanformBatches(ShippingCarrier $shippingCarrier)
    {
        $shipments = Shipment::whereIn('shipping_method_id', $shippingCarrier->shippingMethods->pluck('id'))
            ->whereNotNull('external_manifest_id')
            ->where('external_manifest_id', '!=', 'ignore')
            ->whereDate('created_at', '>', now()->subDays(3)->toDateString())
            ->groupBy('external_manifest_id')
            ->get();

        foreach ($shipments as $shipment) {
            $batch = $this->getBatch($shippingCarrier->credential, $shipment->external_manifest_id);

            if (!empty($batch) && empty($batch->scan_form)) {
                $this->send($shippingCarrier->credential,
                    'POST',
                    '/batches/' . $shipment->external_manifest_id . '/scan_form'
                );
            }
        }
    }

    public function getBatch(EasypostCredential $easypostCredential, $batchId)
    {
        return $this->send($easypostCredential,
            'GET',
            '/batches/' . $batchId
        );
    }
}
