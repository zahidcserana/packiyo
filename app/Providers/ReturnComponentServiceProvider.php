<?php

namespace App\Providers;

use App\Components\ReturnComponent;
use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\Validator;
use App\Models\ReturnItem;
use App\Models\LocationProduct;

class ReturnComponentServiceProvider extends ServiceProvider
{
    public function boot()
    {
        Validator::extend('validate_return_item', function ($attribute, $value, $parameters, $validator) {
            $return = $this->app->request->route('return');
            
            $returnItemIds = $return->returnItems->pluck('id')->toArray();

            return in_array($value, $returnItemIds);
        });
    }

    /**
     * Register services.
     *
     * @return void
     */
    public function register()
    {
        $this->app->singleton('return', function () {
            return new ReturnComponent();
        });
    }

    public function provides()
    {
        return [
            'return'
        ];
    }
}
