<?php

namespace App\Providers;

use App\Components\PaymentComponent;
use Illuminate\Support\ServiceProvider;

class PaymentComponentServiceProvider extends ServiceProvider
{
    public function boot()
    {
    }

    public function register()
    {
        $this->app->singleton('payment', function () {
            return new PaymentComponent();
        });
    }

    public function provides(): array
    {
        return [
            'payment'
        ];
    }
}
