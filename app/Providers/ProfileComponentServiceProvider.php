<?php

namespace App\Providers;

use App\Components\ProfileComponent;
use Illuminate\Support\ServiceProvider;

class ProfileComponentServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     *
     * @return void
     */
    public function register()
    {
        $this->app->singleton('profile', function () {
            return new ProfileComponent();
        });
    }

    public function provides()
    {
        return [
            'profile'
        ];
    }
}
