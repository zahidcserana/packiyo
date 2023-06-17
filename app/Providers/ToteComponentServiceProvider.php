<?php

namespace App\Providers;

use App\Components\ToteComponent;
use App\Models\PickingCart;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\ServiceProvider;

class ToteComponentServiceProvider extends ServiceProvider
{
    public function boot()
    {
        Validator::extend('validate_picking_cart_capacity', static function ($attribute, $value, $parameters, $validator) {
            /** @var PickingCart|null $cart */
            $cart = PickingCart::where('id', $value)->first();

            return count($cart->totes) < $cart->number_of_totes;
        });
    }

    /**
     * Register services.
     *
     * @return void
     */
    public function register()
    {
        $this->app->singleton('tote', function () {
            return new ToteComponent();
        });
    }

    public function provides(): array
    {
        return [
            'tote'
        ];
    }
}
