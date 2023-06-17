<?php

namespace App\Providers;

use App\Components\ShippingMethodComponent;
use Illuminate\Support\ServiceProvider;

class ShippingMethodComponentServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     *
     * @return void
     */
    public function register()
    {
        $this->app->singleton('shippingMethod', function () {
            return new ShippingMethodComponent();
        });
    }

    public function provides()
    {
        return [
            'shippingMethod'
        ];
    }
}
