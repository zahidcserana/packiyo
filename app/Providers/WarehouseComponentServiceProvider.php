<?php

namespace App\Providers;

use App\Components\WarehouseComponent;
use Illuminate\Support\ServiceProvider;

class WarehouseComponentServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     *
     * @return void
     */
    public function register()
    {
        $this->app->singleton('warehouse', function () {
            return new WarehouseComponent();
        });
    }

    public function provides()
    {
        return [
            'warehouse'
        ];
    }
}
