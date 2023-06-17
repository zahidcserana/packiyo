<?php

namespace App\Observers;

use App\Models\ShippingMethodMapping;

class ShippingMethodMappingObserver
{
    /**
     * Handle the ShippingMethodMapping "saved" event.
     *
     * @param  \App\Models\ShippingMethodMapping  $shippingMethodMapping
     * @return void
     */
    public function saved(ShippingMethodMapping $shippingMethodMapping)
    {
        app('shippingMethodMapping')->setShippingMethodToOrders($shippingMethodMapping);
    }
}
