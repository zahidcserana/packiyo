<?php

namespace App\Providers;

use App\Components\LotComponent;
use Illuminate\Support\ServiceProvider;

class LotComponentServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     *
     * @return void
     */
    public function register()
    {
        $this->app->singleton('lot', function () {
            return new LotComponent();
        });
    }

    public function provides()
    {
        return [
            'lot'
        ];
    }
}
