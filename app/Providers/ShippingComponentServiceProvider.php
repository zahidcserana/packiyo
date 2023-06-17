<?php

namespace App\Providers;

use App\Components\PackingComponent;
use App\Components\ShippingComponent;
use Illuminate\Support\ServiceProvider;

class ShippingComponentServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     *
     * @return void
     */
    public function register()
    {
        $this->app->singleton('shipping', function () {
            return new ShippingComponent();
        });
    }

    public function provides()
    {
        return [
            'shipping'
        ];
    }
}
