<?php

namespace App\Providers;

use App\Components\PickingCartComponent;
use Illuminate\Support\ServiceProvider;

class PickingCartComponentServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     *
     * @return void
     */
    public function register()
    {
        $this->app->singleton('pickingCart', function () {
            return new PickingCartComponent();
        });
    }

    public function provides(): array
    {
        return [
            'pickingCart'
        ];
    }
}
