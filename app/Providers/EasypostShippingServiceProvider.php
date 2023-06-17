<?php

namespace App\Providers;

use App\Components\Shipping\Providers\EasypostShippingProvider;
use Illuminate\Support\ServiceProvider;

class EasypostShippingServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     *
     * @return void
     */
    public function register()
    {
        $this->app->singleton('easypostShipping', function () {
            return new EasypostShippingProvider();
        });
    }

    public function provides()
    {
        return [
            'easypostShipping'
        ];
    }
}
