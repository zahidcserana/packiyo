<?php

namespace App\Providers;

use App\Components\WebhookComponent;
use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\Validator;
use App\Models\Webhook;

class WebhookComponentServiceProvider extends ServiceProvider
{
    public function boot()
    {
    }

    public function register()
    {
        $this->app->singleton('webhook', function () {
            return new WebhookComponent();
        });
    }

    public function provides()
    {
        return [
            'webhook'
        ];
    }
}
