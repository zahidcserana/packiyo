<?php

namespace App\Providers;

use App\Components\InventoryLogComponent;
use Illuminate\Support\ServiceProvider;

class InventoryLogComponentServiceProvider extends ServiceProvider
{
    public function register()
    {
        $this->app->singleton('inventoryLog', function () {
            return new InventoryLogComponent();
        });
    }

    public function provides()
    {
        return [
            'inventoryLog'
        ];
    }
}
