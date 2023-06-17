<?php

namespace App\Interfaces;

use App\Http\Requests\Order\StoreReturnRequest as StoreOrderReturnRequest;
use App\Http\Requests\Packing\StoreRequest;
use App\Models\Order;
use App\Models\Return_;
use App\Models\Shipment;
use App\Models\ShippingCarrier;

interface BaseShippingProvider
{
    public function getCarriers(ShippingProviderCredential $credential = null);
    public function ship(Order $order, StoreRequest $storeRequest): ?Shipment;
    public function void(Shipment $shipment): array;
    public function return(Order $order, StoreOrderReturnRequest $storeRequest): ?Return_;
    public function manifest(ShippingCarrier $shippingCarrier);
}
