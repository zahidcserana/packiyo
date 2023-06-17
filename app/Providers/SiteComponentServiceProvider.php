<?php

namespace App\Providers;

use App\Components\SiteComponent;
use Illuminate\Support\ServiceProvider;

class SiteComponentServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     *
     * @return void
     */
    public function register()
    {
        $this->app->singleton('site', function (){
            return new SiteComponent();
        });
    }

    public function provides()
    {
        return [
            'site'
        ];
    }
}
