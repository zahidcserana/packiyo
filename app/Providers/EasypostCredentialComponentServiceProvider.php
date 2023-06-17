<?php

namespace App\Providers;

use App\Components\EasypostCredentialComponent;
use Illuminate\Support\ServiceProvider;

class EasypostCredentialComponentServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     *
     * @return void
     */
    public function register()
    {
        $this->app->singleton('easypostCredential', function () {
            return new EasypostCredentialComponent();
        });
    }

    public function provides()
    {
        return [
            'easypostCredential'
        ];
    }
}
