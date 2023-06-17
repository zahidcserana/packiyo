<?php

namespace App\Providers;

use App\Components\WebshipperCredentialComponent;
use Illuminate\Support\ServiceProvider;

class WebshipperCredentialComponentServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     *
     * @return void
     */
    public function register()
    {
        $this->app->singleton('webshipperCredential', function () {
            return new WebshipperCredentialComponent();
        });
    }

    public function provides()
    {
        return [
            'webshipperCredential'
        ];
    }
}
