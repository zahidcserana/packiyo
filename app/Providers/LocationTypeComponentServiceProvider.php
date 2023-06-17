<?php

namespace App\Providers;

use App\Components\LocationTypeComponent;
use Illuminate\Support\ServiceProvider;

class LocationTypeComponentServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     *
     * @return void
     */
    public function register(): void
    {
        $this->app->singleton('locationType', function (){
            return new LocationTypeComponent();
        });
    }

    public function provides(): array
    {
        return [
            'locationType'
        ];
    }
}
