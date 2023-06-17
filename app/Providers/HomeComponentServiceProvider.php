<?php

namespace App\Providers;

use App\Components\HomeComponent;
use Illuminate\Support\ServiceProvider;

class HomeComponentServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     *
     * @return void
     */
    public function register()
    {
        $this->app->singleton('home', function () {
            return new HomeComponent();
        });
    }

    public function provides()
    {
        return [
            'home'
        ];
    }
}
