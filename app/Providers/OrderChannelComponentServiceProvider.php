<?php

namespace App\Providers;

use App\Components\OrderChannelComponent;
use Illuminate\Support\ServiceProvider;

class OrderChannelComponentServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     *
     * @return void
     */
    public function register()
    {
        $this->app->singleton('orderChannel', function () {
            return new OrderChannelComponent();
        });
    }

    public function provides()
    {
        return [
            'orderChannel'
        ];
    }
}
