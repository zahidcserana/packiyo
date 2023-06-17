<?php

namespace App\Providers;

use App\Components\ShippingBoxComponent;
use Illuminate\Support\ServiceProvider;

class ShippingBoxComponentServiceComponent extends ServiceProvider
{
    /**
     * Register services.
     *
     * @return void
     */
    public function register()
    {
        $this->app->singleton('shippingBox', function () {
            return new ShippingBoxComponent();
        });
    }

    public function provides()
    {
        return [
            'shippingBox'
        ];
    }
}
