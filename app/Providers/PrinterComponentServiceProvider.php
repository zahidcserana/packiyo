<?php

namespace App\Providers;

use App\Components\PrinterComponent;
use Illuminate\Support\ServiceProvider;

class PrinterComponentServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     *
     * @return void
     */
    public function register()
    {
        $this->app->singleton('printer', function () {
            return new PrinterComponent();
        });
    }

    public function provides()
    {
        return [
            'printer'
        ];
    }
}
