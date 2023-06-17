<?php

namespace App\Providers;

use App\Components\OrderStatusComponent;
use Illuminate\Support\ServiceProvider;

class OrderStatusComponentServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     *
     * @return void
     */
    public function register()
    {
        $this->app->singleton('orderStatus', function () {
            return new OrderStatusComponent();
        });
    }

    public function provides()
    {
        return [
            'orderStatus'
        ];
    }
}
