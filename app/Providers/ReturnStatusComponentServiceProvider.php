<?php

namespace App\Providers;

use App\Components\ReturnStatusComponent;
use Illuminate\Support\ServiceProvider;

class ReturnStatusComponentServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     *
     * @return void
     */
    public function register()
    {
        $this->app->singleton('returnStatus', function () {
            return new ReturnStatusComponent();
        });
    }

    public function provides()
    {
        return [
            'returnStatus'
        ];
    }
}
