<?php

namespace App\Providers;

use App\Components\SupplierComponent;
use Illuminate\Support\ServiceProvider;

class SupplierComponentServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     *
     * @return void
     */
    public function register()
    {
        $this->app->singleton('supplier', function () {
            return new SupplierComponent();
        });
    }

    public function provides()
    {
        return [
            'supplier'
        ];
    }
}
