<?php

namespace App\Components;

use App\Components\Shipping\Providers\DummyShippingProvider;
use App\Components\Shipping\Providers\EasypostShippingProvider;
use App\Components\Shipping\Providers\WebshipperShippingProvider;
use App\Exceptions\ShippingException;
use App\Interfaces\BaseShippingProvider;
use App\Models\Order;
use App\Models\Shipment;
use App\Models\ShippingMethod;
use Illuminate\Http\Request;
use Illuminate\Support\Arr;

class ShippingComponent extends BaseComponent
{
    public const SHIPPING_CARRIER_SERVICE_DUMMY = 'dummy';
    public const SHIPPING_CARRIER_SERVICE_EASYPOST = 'easypost';
    public const SHIPPING_CARRIER_SERVICE_WEBSHIPPER = 'webshipper';

    public const SHIPPING_CARRIERS = [
        ShippingComponent::SHIPPING_CARRIER_SERVICE_DUMMY => DummyShippingProvider::class,
        ShippingComponent::SHIPPING_CARRIER_SERVICE_EASYPOST => EasypostShippingProvider::class,
        ShippingComponent::SHIPPING_CARRIER_SERVICE_WEBSHIPPER => WebshipperShippingProvider::class
    ];

    public function getCarriers()
    {
        foreach (self::SHIPPING_CARRIERS as $shippingCarrierClass) {
            /** @var BaseShippingProvider $shippingCarrierProvider */
            $shippingCarrierProvider = new $shippingCarrierClass();

            $shippingCarrierProvider->getCarriers();
        }
    }

    /**
     * @param Order $order
     * @param Request $storeRequest
     * @return mixed
     * @throws ShippingException
     */
    public function ship(Order $order, Request $storeRequest)
    {
        $input = $storeRequest->all();

        $shippingMethod = ShippingMethod::find($input['shipping_method_id']);

        $shippingProviderClass = Arr::get(
            self::SHIPPING_CARRIERS,
            $shippingMethod->shippingCarrier->carrier_service ?? self::SHIPPING_CARRIERS[self::SHIPPING_CARRIER_SERVICE_DUMMY]
        );

        if (!$shippingProviderClass) {
            $shippingProviderClass = self::SHIPPING_CARRIERS[self::SHIPPING_CARRIER_SERVICE_DUMMY];
        }

        return (new $shippingProviderClass)->ship($order, $storeRequest);
    }

    public function return(Order $order, $request)
    {
        $input = $request->all();

        $shippingMethod = ShippingMethod::find($input['shipping_method_id']);

        $shippingProviderClass = Arr::get(self::SHIPPING_CARRIERS, $shippingMethod->shippingCarrier->carrier_service ?? self::SHIPPING_CARRIERS[self::SHIPPING_CARRIER_SERVICE_DUMMY]);

        if (!$shippingProviderClass) {
            $shippingProviderClass = self::SHIPPING_CARRIERS[self::SHIPPING_CARRIER_SERVICE_DUMMY];
        }

        return (new $shippingProviderClass)->return($order, $request);
    }

    public function void(Shipment $shipment)
    {
        $shippingProviderClass = Arr::get(
            self::SHIPPING_CARRIERS,
            $shipment->shippingMethod->shippingCarrier->carrier_service ?? self::SHIPPING_CARRIERS[self::SHIPPING_CARRIER_SERVICE_DUMMY]
        );

        if (!$shippingProviderClass) {
            $shippingProviderClass = self::SHIPPING_CARRIERS[self::SHIPPING_CARRIER_SERVICE_DUMMY];
        }

        $shippingProvider = new $shippingProviderClass;

        $shipmentVoid = $shippingProvider->void($shipment);

        if ($shipmentVoid['success']) {
            app('order')->voidShipment($shipment);
        }

        return $shipmentVoid;
    }
}
