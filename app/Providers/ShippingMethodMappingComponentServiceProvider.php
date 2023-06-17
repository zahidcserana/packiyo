<?php

namespace App\Providers;

use App\Components\ShippingMethodMappingComponent;
use Illuminate\Support\ServiceProvider;

class ShippingMethodMappingComponentServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     *
     * @return void
     */
    public function register()
    {
        $this->app->singleton('shippingMethodMapping', function () {
            return new ShippingMethodMappingComponent();
        });
    }

    public function provides()
    {
        return [
            'shippingMethodMapping'
        ];
    }
}
