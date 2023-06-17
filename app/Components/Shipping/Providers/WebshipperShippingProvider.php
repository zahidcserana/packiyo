<?php

namespace App\Components\Shipping\Providers;

use App\Components\ReturnComponent;
use App\Components\ShippingComponent;
use App\Exceptions\ShippingException;
use App\Http\Requests\Packing\PackageItemRequest;
use App\Http\Requests\Shipment\ShipItemRequest;
use App\Http\Requests\ShippingMethod\DropPointRequest;
use App\Interfaces\BaseShippingProvider;
use App\Interfaces\ShippingProviderCredential;
use App\Models\Currency;
use App\Models\Customer;
use App\Models\CustomerSetting;
use App\Models\Location;
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
use App\Models\ShippingCarrier;
use App\Models\ShippingMethod;
use App\Models\WebshipperCredential;
use GuzzleHttp\Client;
use GuzzleHttp\Exception\RequestException;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Log;
use Carbon\Carbon;

class WebshipperShippingProvider implements BaseShippingProvider
{
    /**
     * @param ShippingProviderCredential|null $credential
     * @return void
     * @throws ShippingException
     * @throws \JsonException
     */
    public function getCarriers(ShippingProviderCredential $credential = null)
    {
        $carrierService = array_search(get_class($this), ShippingComponent::SHIPPING_CARRIERS);
        $credentials = new Collection();

        if (!is_null($credential)) {
            $credentials->add($credential);
        } else {
            $credentials = WebshipperCredential::all();
        }

        $webshipperCarrierIds = [];
        $webShipperRateIds = [];

        foreach ($credentials as $credential) {
            if ($credential->order_channel_id) {
                $orderChannelId = $credential->order_channel_id;

                $response = $this->send($credential, 'GET', '/order_channels/' . $orderChannelId . '?include=shipping_rates,shipping_rates.carrier');

                if (!Arr::has($response, 'data')) {
                    return;
                }

                $shippingRates = Arr::get($response, 'data.relationships.shipping_rates.data', []);
                $included = Arr::get($response, 'included', []);

                foreach ($shippingRates as $shippingRateRelation) {
                    $shippingRate = Arr::first($included, function($includedItem) use ($shippingRateRelation) {
                        return Arr::get($includedItem, 'id') == Arr::get($shippingRateRelation, 'id') &&
                            Arr::get($includedItem, 'type') == Arr::get($shippingRateRelation, 'type');
                    });

                    if ($shippingRate) {
                        $carrierRelation = Arr::get($shippingRate, 'relationships.carrier.data');

                        $carrier = Arr::first($included, function($includedItem) use ($carrierRelation) {
                            return Arr::get($includedItem, 'id') == Arr::get($carrierRelation, 'id') &&
                                Arr::get($includedItem, 'type') == Arr::get($carrierRelation, 'type');
                        });

                        if ($carrier) {
                            $externalCarrierId = (int)Arr::get($carrier, 'id');
                            $carrierName = Arr::get($carrier, 'attributes.alias');
                            $rateId = (int)Arr::get($shippingRate, 'id');
                            $rateName = Arr::get($shippingRate, 'attributes.name');
                            $rateHasPickupPoints = (bool) Arr::get($shippingRate, 'attributes.require_drop_point');

                            $webshipperCarrierIds[] = $externalCarrierId;
                            $webShipperRateIds[] = $rateId;

                            $shippingCarrier = ShippingCarrier::withTrashed()
                                ->where('customer_id', $credential->customer_id)
                                ->where('carrier_service', $carrierService)
                                ->whereJsonContains('settings', ['external_carrier_id' => $externalCarrierId])
                                ->first();

                            if (!$shippingCarrier) {
                                $shippingCarrier = ShippingCarrier::create([
                                    'customer_id' => $credential->customer_id,
                                    'carrier_service' => $carrierService,
                                    'settings' => [
                                        'external_carrier_id' => $externalCarrierId
                                    ]
                                ]);

                                $shippingCarrier->credential()->associate($credential);
                            }

                            $shippingCarrier->name = $carrierName;

                            $shippingCarrier->save();
                            $shippingCarrier->restore();

                            $shippingMethod = ShippingMethod::withTrashed()
                                ->where('shipping_carrier_id', $shippingCarrier->id)
                                ->whereJsonContains('settings', ['external_method_id' => $rateId])
                                ->first();

                            if (!$shippingMethod) {
                                $shippingMethod = ShippingMethod::create([
                                    'shipping_carrier_id' => $shippingCarrier->id
                                ]);
                            }

                            $shippingMethod->name = $rateName;
                            $shippingMethod->settings = [
                                'external_method_id' => $rateId,
                                'has_drop_points' => $rateHasPickupPoints
                            ];

                            $shippingMethod->save();
                            $shippingMethod->restore();
                        }
                    }
                }
            }

            $customerWebshipperCarriers = ShippingCarrier::with('shippingMethods')
                ->where('customer_id', $credential->customer_id)
                ->where('carrier_service', $carrierService)
                ->get();

            foreach ($customerWebshipperCarriers as $webshipperCarrier) {
                foreach ($webshipperCarrier->shippingMethods as $shippingMethod) {
                    if (!in_array($shippingMethod->settings['external_method_id'], $webShipperRateIds)) {
                        $shippingMethod->delete();
                    }
                }

                if (!in_array($webshipperCarrier->settings['external_carrier_id'], $webshipperCarrierIds)) {
                    $webshipperCarrier->delete();
                }
            }
        }
    }

    public function shippingResponse($response, $shipment)
    {
        if (!is_null($response)) {
            if (is_array($response) && isset($response['data'])) {
                $shipmentWebShipperId = $response['data']['id'];
                $shipment->update(
                    [
                        'processing_status' => Shipment::PROCESSING_STATUS_SUCCESS,
                        'webshipper_shipment_id' => $shipmentWebShipperId
                    ]
                );
            } else {
                $response = json_decode($response, 1);
                if (isset($response['errors']) && count($response['errors']) > 0) {
                    $errorTitle = $response['errors'][0]['title'];
                    $errorDetail = $response['errors'][0]['detail'];
                } else {
                    $errorTitle = 'Unknown error';
                    $errorDetail = 'Unknown error';
                }
                $shipment->update(
                    [
                        'processing_status' => Shipment::PROCESSING_STATUS_FAILED
                    ]
                );
            }
        }
    }

    /**
     * @throws \JsonException
     * @throws ShippingException
     */
    public function ship(Order $order, $storeRequest): ?Shipment
    {
        $input = $storeRequest->all();

        $shippingRateId = $input['shipping_method_id'];
        $shippingMethod = ShippingMethod::find($shippingRateId);

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

        $shipmentRequestBody = $this->createShipmentRequestBody($order, $storeRequest, $shippingMethod);

        $response = $this->send(
            $shippingMethod->shippingCarrier->credential,
            'POST',
            '/shipments?include=labels',
            $shipmentRequestBody
        );

        if ($response) {
            $shipmentWebShipperId = $response['data']['id'];

            $shipment = new Shipment();
            $shipment->user_id = auth()->user()->id ?? 1;
            $shipment->order_id = $order->id;
            $shipment->shipping_method_id = $shippingMethod->id;
            $shipment->processing_status = Shipment::PROCESSING_STATUS_SUCCESS;
            $shipment->external_shipment_id = $shipmentWebShipperId;

            if (Arr::get($input, 'drop_point_id')) {
                $shipment->drop_point_id = Arr::get($input, 'drop_point_id');
            }

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

            $this->storeShipmentLabelAndTracking($shipment, $response);

            if (customer_settings($shipment->order->customer_id, CustomerSetting::CUSTOMER_SETTING_AUTO_RETURN_LABEL) === '1') {
                $shippingMethod = app('shippingMethodMapping')->returnShippingMethod($order) ?? $shippingMethod;

                $this->createAutoReturnLabels($shipmentRequestBody, $shippingMethod, $shipment);
            }

            return $shipment;
        }

        return null;
    }

    public function return(Order $order, $storeRequest): ?Return_
    {
        $input = $storeRequest->all();
        $input['number'] = Return_::getUniqueIdentifier(ReturnComponent::NUMBER_PREFIX, $input['warehouse_id']);

        $shippingRateId = $input['shipping_method_id'];
        $shippingMethod = ShippingMethod::find($shippingRateId);

        $requestBody = $this->createReturnRequestBody($order, $storeRequest, $shippingMethod);

        $response = $this->send(
            $shippingMethod->shippingCarrier->credential,
            'POST',
            '/shipments?include=labels',
            $requestBody
        );

        if ($response) {
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

            $this->storeReturnLabelAndTracking($return, $response, $input['own_label']);

            return $return;

        }

        return null;
    }

    public function createShipmentRequestBody(Order $order, $storeRequest, ShippingMethod $shippingMethod)
    {
        $input = $storeRequest->all();

        $orderItemsToShip = [];
        $packageItemRequests = [];

        foreach ($input['order_items'] as $record) {
            $shipItemRequest = ShipItemRequest::make($record);
            $orderItem = OrderItem::find($record['order_item_id']);
            $orderItemsToShip[] = ['orderItem' => $orderItem, 'shipRequest' => $shipItemRequest];
        }

        $packingState = json_decode($input['packing_state'], true);

        foreach ($packingState as $packingStateItem) {
            $packageItemRequest = PackageItemRequest::make($packingStateItem);
            $packageItemRequests[] = $packageItemRequest;
        }

        $customerAddress = $order->customer->contactInformation;

        $request['data']['type'] = 'shipments';
        $request['data']['attributes']['reference'] = $order->number;

        if ($order->currency) {
            $currency = $order->currency->code;
        } else {
            $customerCurrency = Currency::find(customer_settings($order->customer->id, CustomerSetting::CUSTOMER_SETTING_CURRENCY));

            if ($customerCurrency) {
                $currency = $customerCurrency->code;
            }
        }

        foreach ($packageItemRequests as $packageToItem) {
            $customsLines = [];
            $dimensions['height'] = $packageToItem->height;
            $dimensions['width'] = $packageToItem->width;
            $dimensions['length'] = $packageToItem->_length;
            $dimensions['unit'] = $packageToItem->unit;

            $itemsPackedInThisPackage = [];

            $tmpCountArr = [];

            foreach( $packageToItem->items as $itemsIdLocArr ){
                $tmpCountArr[] = $itemsIdLocArr['orderItem'];//.'-'.$itemsIdLocArr['location'];
            }

            $packItemLocationId = null;

            foreach( $packageToItem->items as $packItem ){
                $packItemId = $packItem['orderItem'];

                if (is_null($packItemLocationId)) {
                    $packItemLocationId = $packItem['location'];

                    /** @var Location $senderLocation */
                    $senderLocation = Location::find($packItemLocationId);
                    $customerWarehouseAddress = $senderLocation->warehouse->contactInformation;
                }

                foreach ($orderItemsToShip as $orderItemToShip) {
                    if ($orderItemToShip['orderItem']->id == $packItemId && !in_array($packItemId, $itemsPackedInThisPackage)) {
                        $itemsPackedInThisPackage[] = $packItemId;//.'-'.$packItemLocationId;

                        $description = $orderItemToShip['orderItem']->product->customs_description;

                        if (empty($description)) {
                            $description = $orderItemToShip['orderItem']->name;
                        }

                        $item['sku'] = $orderItemToShip['orderItem']->sku;
                        $item['description'] = mb_substr($description, 0, 50);

                        $tmp = array_count_values($tmpCountArr);
                        $item['quantity'] = $tmp[$packItemId]; //.'-'.$packItemLocationId

                        $item['unit_price'] = $orderItemToShip['orderItem']->price;

                        if (isset($currency)) {
                            $item['currency'] = $currency;
                        }

                        if ($orderItemToShip['orderItem']->product->country) {
                            $item['country_of_origin'] = $orderItemToShip['orderItem']->product->country->iso_3166_2;
                        }

                        $item['tarif_number'] = $orderItemToShip['orderItem']->product->hs_code;
                        $item['weight'] = $orderItemToShip['orderItem']->product->weight * $tmp[$packItemId];
                        $item['weight_unit'] = customer_settings($order->customer->id, CustomerSetting::CUSTOMER_SETTING_WEIGHT_UNIT, 'kg');

                        $customsLines[] = $item;
                        break;
                    }
                }
            }

            $request['data']['attributes']['packages'][] = [
                'customs_lines' => $customsLines,
                'dimensions' => $dimensions,
                'weight' => $packageToItem->weight,
                'weight_unit' => customer_settings($order->customer->id, CustomerSetting::CUSTOMER_SETTING_WEIGHT_UNIT, 'kg')
            ];
        }

        $contactInformationData = $order->shippingContactInformation->toArray();

        $deliveryAddress['att_contact'] = $contactInformationData['name'];
        $deliveryAddress['company_name'] = $contactInformationData['company_name'] ?? null;
        $deliveryAddress['address_1'] = $contactInformationData['address'];
        $deliveryAddress['zip'] = $contactInformationData['zip'];
        $deliveryAddress['city'] = $contactInformationData['city'];
        $deliveryAddress['country_code'] = $order->shippingContactInformation->country->iso_3166_2 ?? null;
        $deliveryAddress['email'] = $contactInformationData['email'];
        $deliveryAddress['phone'] = $contactInformationData['phone'];
        $deliveryAddress['address_type'] = 'recipient';
        $request['data']['attributes']['delivery_address'] = $deliveryAddress;

        $senderAddress['att_contact'] = $customerAddress->name;
        $senderAddress['company_name'] = $customerWarehouseAddress->company_name ?? $customerAddress->company_name;
        $senderAddress['address_1'] = $customerWarehouseAddress->address ?? $customerAddress->address;
        $senderAddress['zip'] = $customerWarehouseAddress->zip ?? $customerAddress->zip;
        $senderAddress['city'] = $customerWarehouseAddress->city ?? $customerAddress->city;
        $senderAddress['country_code'] = $customerWarehouseAddress->country->iso_3166_2 ?? $customerAddress->country->iso_3166_2;
        $senderAddress['email'] = $customerWarehouseAddress->email ?? $customerAddress->email;
        $senderAddress['phone'] = $customerWarehouseAddress->phone ?? $customerAddress->phone;
        $request['data']['attributes']['sender_address'] = $senderAddress;

        if ($input['drop_point_id']) {
            $request['data']['attributes']['drop_point']['drop_point_id'] = $input['drop_point_id'];
        }

        $request['data']['relationships']['shipping_rate']['data']['id'] = $shippingMethod->settings['external_method_id'];
        $request['data']['relationships']['shipping_rate']['data']['type'] = 'shipping_rates';

        $request['data']['relationships']['carrier']['data']['id'] = $shippingMethod->shippingCarrier->settings['external_carrier_id'];
        $request['data']['relationships']['carrier']['data']['type'] = 'carriers';

        return $request;
    }

    public function createReturnRequestBody(Order $order, $storeRequest, ShippingMethod $shippingMethod)
    {
        $input = $storeRequest->all();
        $weightUnit = customer_settings($order->customer->id, CustomerSetting::CUSTOMER_SETTING_WEIGHT_UNIT, Customer::WEIGHT_UNIT_DEFAULT);
        $defaultBox = $order->customer->shippingBoxes->first();

        $orderItemsArr = [];
        $customsLines = [];
        $orderItemsToShip = [];
        $totalWeight = 0;

        foreach ($input['order_items'] as $record)
        {
            $shipItemRequest = ShipItemRequest::make($record);
            $orderItem = OrderItem::find($record['order_item_id']);
            $orderItemsToShip[] = ['orderItem' => $orderItem, 'shipRequest' => $shipItemRequest];

            $totalWeight += $orderItem->weight;
            $orderItemsArr[] = [
                'orderItem' => $record['order_item_id'],
                'location' => $record['location_id'],
                'tote' => $record['tote_id'],
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

        $packageItemRequest = PackageItemRequest::make($packingStateItem);

        $request['data']['type'] = 'shipments';
        $request['data']['attributes']['reference'] = $order->number;

        $dimensions['height'] = $packageItemRequest->height;
        $dimensions['width'] = $packageItemRequest->width;
        $dimensions['length'] = $packageItemRequest->_length;
        $dimensions['unit'] = null;

        $itemsPackedInThisPackage = [];

        $tmpCountArr = [];

        foreach( $packageItemRequest->items as $itemsIdLocArr ){
            $tmpCountArr[] = $itemsIdLocArr['orderItem'];//.'-'.$itemsIdLocArr['location'];
        }

        $packItemLocationId = null;

        if ($order->currency) {
            $currency = $order->currency->code;
        } else {
            $customerCurrency = Currency::find(customer_settings($order->customer->id, CustomerSetting::CUSTOMER_SETTING_CURRENCY));

            if ($customerCurrency) {
                $currency = $customerCurrency->code;
            }
        }

        foreach ($packageItemRequest->items as $packItem) {
            $packItemId = $packItem['orderItem'];

            if (is_null($packItemLocationId)) {
                $packItemLocationId = $packItem['location'];

                /** @var Location $senderLocation */
                $senderLocation = Location::find($packItemLocationId);
                $customerWarehouseAddress = $senderLocation->warehouse->contactInformation;
            }

            foreach ($orderItemsToShip as $orderItemToShip) {
                if ($orderItemToShip['orderItem']->id == $packItemId && !in_array($packItemId, $itemsPackedInThisPackage)) {
                    $itemsPackedInThisPackage[] = $packItemId;//.'-'.$packItemLocationId;

                    $description = $orderItemToShip['orderItem']->product->customs_description;

                    if (empty($description)) {
                        $description = $orderItemToShip['orderItem']->name;
                    }

                    $item['sku'] = $orderItemToShip['orderItem']->sku;
                    $item['description'] = mb_substr($description, 0, 50);

                    $tmp = array_count_values($tmpCountArr);
                    $item['quantity'] = $tmp[$packItemId]; //.'-'.$packItemLocationId

                    $item['unit_price'] = $orderItemToShip['orderItem']->price;

                    if ($currency) {
                        $item['currency'] = $currency;
                    }

                    if ($orderItemToShip['orderItem']->product->country) {
                        $item['country_of_origin'] = $orderItemToShip['orderItem']->product->country->iso_3166_2;
                    }

                    $item['tarif_number'] = $orderItemToShip['orderItem']->product->hs_code;
                    $item['weight'] = $orderItemToShip['orderItem']->product->weight * $tmp[$packItemId];

                    $customsLines[] = $item;
                    break;
                }
            }
        }

        $request['data']['attributes']['packages'][] = [
            'customs_lines' => $customsLines,
            'dimensions' => $dimensions,
            'weight' => $packageItemRequest->weight,
            'weight_unit' => $weightUnit,
        ];

        $customerAddress = $order->customer->contactInformation;
        $contactInformationData = $order->shippingContactInformation->toArray();
        $warehouse = $order->customer->parent_id ? $order->customer->parent->warehouses->first() : $order->customer->warehouses->first();
        $customerWarehouseAddress = $warehouse->contactInformation;

        // Receiver (warehouse)
        $deliveryAddress['att_contact'] = $customerAddress->name;
        $deliveryAddress['company_name'] = $customerWarehouseAddress->company_name ?? $customerAddress->company_name;
        $deliveryAddress['address_1'] = $customerWarehouseAddress->address ?? $customerAddress->address;
        $deliveryAddress['zip'] = $customerWarehouseAddress->zip ?? $customerAddress->zip;
        $deliveryAddress['city'] = $customerWarehouseAddress->city ?? $customerAddress->city;
        $deliveryAddress['country_code'] = $customerWarehouseAddress->country->iso_3166_2 ?? $customerAddress->country->iso_3166_2;
        $deliveryAddress['email'] = $customerWarehouseAddress->email ?? $customerAddress->email;
        $deliveryAddress['phone'] = $customerWarehouseAddress->phone ?? $customerAddress->phone;
        $deliveryAddress['address_type'] = 'recipient';
        $request['data']['attributes']['delivery_address'] = $deliveryAddress;
        // Sender (customer)
        $senderAddress['att_contact'] = $contactInformationData['name'];
        $senderAddress['company_name'] = $contactInformationData['company_name'] ?? null;
        $senderAddress['address_1'] = $contactInformationData['address'];
        $senderAddress['zip'] = $contactInformationData['zip'];
        $senderAddress['city'] = $contactInformationData['city'];
        $senderAddress['country_code'] = $order->shippingContactInformation->country->iso_3166_2 ?? null;
        $senderAddress['email'] = $contactInformationData['email'];
        $senderAddress['phone'] = $contactInformationData['phone'];
        $request['data']['attributes']['sender_address'] = $senderAddress;

        if ($input['drop_point_id']) {
            $request['data']['attributes']['drop_point']['drop_point_id'] = $input['drop_point_id'];
        }

        $request['data']['relationships']['shipping_rate']['data']['id'] = $shippingMethod->settings['external_method_id'];
        $request['data']['relationships']['shipping_rate']['data']['type'] = 'shipping_rates';

        return $request;
    }

    private function storeReturnLabelAndTracking(Return_ $return, $response, $ownLabel)
    {
        foreach (Arr::get($response, 'included', []) as $included) {
            if (Arr::get($included, 'type') === 'labels' && $ownLabel === '0') {
                ReturnLabel::create([
                    'return_id' => $return->id,
                    'size' => Arr::get($included, 'attributes.label_size'),
                    'content' => Arr::get($included, 'attributes.base64'),
                    'type' => 'pdf'
                ]);
            }
        }

        foreach (Arr::get($response, 'data.attributes.tracking_links', []) as $trackingLink) {
            ReturnTracking::create([
                'return_id' => $return->id,
                'tracking_number' => Arr::get($trackingLink, 'number'),
                'tracking_url' => Arr:: get($trackingLink, 'url'),
            ]);
        }
    }

    private function storeShipmentLabelAndTracking(Shipment $shipment, $carrierResponse)
    {
        foreach (Arr::get($carrierResponse, 'included', []) as $included) {
            if (Arr::get($included, 'type') === 'labels') {
                ShipmentLabel::create([
                    'shipment_id' => $shipment->id,
                    'size' => Arr::get($included, 'attributes.label_size'),
                    'content' => Arr::get($included, 'attributes.base64'),
                    'document_type' => 'pdf',
                    'type' => ShipmentLabel::TYPE_SHIPPING
                ]);
            }
        }

        foreach (Arr::get($carrierResponse, 'data.attributes.tracking_links', []) as $trackingLink) {
            ShipmentTracking::create([
                'shipment_id' => $shipment->id,
                'tracking_number' => Arr::get($trackingLink, 'number'),
                'tracking_url' => Arr:: get($trackingLink, 'url'),
                'type' => ShipmentTracking::TYPE_SHIPPING
            ]);
        }
    }

    private function send(WebshipperCredential $webshipperCredential, $method, $endpoint, $data = null, $returnException = true)
    {
        Log::info('[Webshipper] send', [
            'webshipper_credential_id' => $webshipperCredential->id,
            'method' => $method,
            'endpoint' => $endpoint,
            'data' => $data
        ]);

        $credentials = $this->getApiCredentials($webshipperCredential);
        $url = $credentials['baseUrl'] . $endpoint;

        $client = new Client([
            'headers' => [
                'Content-Type' => 'application/vnd.api+json',
                'Authorization' => 'Bearer ' . Arr::get($credentials, 'apiKey'),
            ]
        ]);

        try {
            Log::debug($url);
            $response = $client->request($method, $url, $method == 'GET' ? [] : ['body' => json_encode($data)]);
            $body = json_decode($response->getBody()->getContents(), true, 512, JSON_THROW_ON_ERROR);

            Log::info('[Webshipper] response', [$body]);

            return $body;
        } catch (RequestException $exception) {
            Log::error('[Webshipper] exception thrown', [$exception->getResponse()->getBody()]);

            if ($returnException) {
                throw new ShippingException($exception->getResponse()->getBody());
            }
        }

        return null;
    }

    private function getApiCredentials(WebshipperCredential $webshipperCredential)
    {
        $baseUrl = '';
        $apiKey = '';

        if ($webshipperCredential) {
            $baseUrl = rtrim($webshipperCredential->api_base_url, '/');
            $apiKey = $webshipperCredential->api_key;
        }

        return compact('baseUrl', 'apiKey');
    }

    public function getDropPoints(DropPointRequest $request): JsonResponse
    {
        $input = $request->validated();

        $shippingMethod = ShippingMethod::find($input['shipping_method_id']);
        $order = Order::find($input['order_id']);

        $deliveryAddress['address_1'] = $input['address'];
        $deliveryAddress['zip'] = $input['zip'];
        $deliveryAddress['city'] = $input['city'];
        $deliveryAddress['country_code'] = $input['country_code'];

        try {
            $dropPoints = $this->send($shippingMethod->shippingCarrier->credential, 'POST', '/drop_point_locators', $this->dropPointLocatorRequestBody($shippingMethod, $deliveryAddress)) ?? [];
        } catch (\Exception $exception) {
            $dropPoints = $this->send($shippingMethod->shippingCarrier->credential, 'POST', '/drop_point_locators', $this->dropPointLocatorRequestBody($shippingMethod, $deliveryAddress, $shippingMethod->settings['external_method_id'])) ?? [];
        }

        $results = [];

        if (Arr::exists($dropPoints, 'data')) {
            foreach ($dropPoints['data']['attributes']['drop_points'] as $dropPoint) {
                $results[] = [
                    'id' => $dropPoint['drop_point_id'],
                    'text' => $dropPoint['name'] . ', ' . $dropPoint['address_1'] . ', ' . $dropPoint['zip'] . ' ' . $dropPoint['city']
                ];
            }
        }

        if (Arr::exists($input, 'preselect')) {
            if ($order->drop_point_id) {
                $extractedDropPointId = $order->drop_point_id;
            } else {
                $matches = [];
                preg_match('/(?<=_)[\d]+/', $order->shipping_method_code, $matches);

                if (isset($matches[0])) {
                    $extractedDropPointId = (int)$matches[0];
                }
            }
        }

        if (Arr::exists($input, 'q')) {
            $search = $input['q'];
            $extractedDropPointId = null;
        }

        if (isset($extractedDropPointId, $results)) {
            $filteredResults = [];

            foreach ($results as $result) {
                if ((int) $result['id'] === $extractedDropPointId) {
                    $filteredResults[] = $result;
                }
            }

            $results = $filteredResults;
        } elseif (isset($search, $results)) {
            $filteredResults = [];

            foreach ($results as $result) {
                if (str_contains(strtolower($result['text']), $search)) {
                    $filteredResults[] = $result;
                }
            }

            $results = $filteredResults;
        }

        return response()->json([
            'results' => $results
        ]);
    }

    private function dropPointLocatorRequestBody(ShippingMethod $shippingMethod, array $deliveryAddress, $shippingRateId = null): array
    {
        $request['data']['type'] = 'drop_point_locators';

        if ($shippingRateId) {
            $request['data']['attributes']['shipping_rate_id'] = $shippingRateId;
        } else {
            $request['data']['attributes']['carrier_id'] = $shippingMethod->shippingCarrier->settings['external_carrier_id'];
            $request['data']['attributes']['service_code'] = '';
        }
        $request['data']['attributes']['delivery_address'] = $deliveryAddress;

        return $request;
    }

    public function void(Shipment $shipment): array
    {
        $shipment->voided_at = Carbon::now();

        $shipment->saveQuietly();

        return ['success' => true, 'message' => __('Shipment successfully voided.')];
    }

    /**
     * @param array $shipmentRequestBody
     * @param mixed $shippingMethod
     * @param Shipment $shipment
     * @return void
     * @throws ShippingException
     * @throws \JsonException
     */
    private function createAutoReturnLabels(array $shipmentRequestBody, mixed $shippingMethod, Shipment $shipment): void
    {
        $autoReturnLabelRequestBody = $this->createAutoReturnLabelRequestBody($shipmentRequestBody);

        $response = $this->send(
            $shippingMethod->shippingCarrier->credential,
            'POST',
            '/shipments?include=labels',
            $autoReturnLabelRequestBody,
            false
        );

        if ($response) {
            foreach (Arr::get($response, 'included', []) as $included) {
                if (Arr::get($included, 'type') === 'labels') {
                    ShipmentLabel::create([
                        'shipment_id' => $shipment->id,
                        'size' => Arr::get($included, 'attributes.label_size'),
                        'content' => Arr::get($included, 'attributes.base64'),
                        'document_type' => 'pdf',
                        'type' => ShipmentLabel::TYPE_RETURN
                    ]);
                }
            }

            foreach (Arr::get($response, 'data.attributes.tracking_links', []) as $trackingLink) {
                ShipmentTracking::create([
                    'shipment_id' => $shipment->id,
                    'tracking_number' => Arr::get($trackingLink, 'number'),
                    'tracking_url' => Arr:: get($trackingLink, 'url'),
                    'type' => ShipmentTracking::TYPE_RETURN
                ]);
            }
        }
    }

    /**
     * @param $shipmentRequestBody
     * @return array
     */
    private function createAutoReturnLabelRequestBody($shipmentRequestBody): array
    {
        $deliveryAddress = $shipmentRequestBody['data']['attributes']['delivery_address'];

        $senderAddress = $shipmentRequestBody['data']['attributes']['sender_address'];

        $shipmentRequestBody['data']['attributes']['delivery_address'] = $senderAddress;

        $shipmentRequestBody['data']['attributes']['sender_address'] = $deliveryAddress;

        return $shipmentRequestBody;
    }

    public function manifest(ShippingCarrier $shippingCarrier)
    {
        // TODO: Implement manifest() method.
    }
}

