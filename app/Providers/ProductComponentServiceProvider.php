<?php

namespace App\Providers;

use App\Components\ProductComponent;
use Illuminate\Support\ServiceProvider;

class ProductComponentServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     *
     * @return void
     */
    public function register()
    {
        $this->app->singleton('product', function () {
            return new ProductComponent();
        });
    }

    public function provides()
    {
        return [
            'product'
        ];
    }
}
