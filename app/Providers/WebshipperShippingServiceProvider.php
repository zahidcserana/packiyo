<?php

namespace App\Providers;

use App\Components\Shipping\Providers\WebshipperShippingProvider;
use Illuminate\Support\ServiceProvider;

class WebshipperShippingServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     *
     * @return void
     */
    public function register()
    {
        $this->app->singleton('webshipperShipping', function () {
            return new WebshipperShippingProvider();
        });
    }

    public function provides()
    {
        return [
            'webshipperShipping'
        ];
    }
}
