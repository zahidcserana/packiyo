<?php

namespace App\Providers;

use App\Components\PackingComponent;
use Illuminate\Support\ServiceProvider;

class PackingComponentServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     *
     * @return void
     */
    public function register()
    {
        $this->app->singleton('packing', function () {
            return new PackingComponent();
        });
    }

    public function provides()
    {
        return [
            'packing'
        ];
    }
}
