<?php

namespace App\Providers;

use App\Components\PurchaseOrderStatusComponent;
use Illuminate\Support\ServiceProvider;

class PurchaseOrderStatusComponentServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     *
     * @return void
     */
    public function register()
    {
        $this->app->singleton('purchaseOrderStatus', function () {
            return new PurchaseOrderStatusComponent();
        });
    }

    public function provides()
    {
        return [
            'purchaseOrderStatus'
        ];
    }
}
