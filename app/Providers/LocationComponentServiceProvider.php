<?php

namespace App\Providers;

use App\Components\LocationComponent;
use Illuminate\Support\ServiceProvider;

class LocationComponentServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     *
     * @return void
     */
    public function register()
    {
        $this->app->singleton('location', function (){
            return new LocationComponent();
        });
    }

    public function provides()
    {
        return [
            'location'
        ];
    }
}
