<?php

namespace App\Providers;

use App\Components\EditColumnComponent;
use Illuminate\Support\ServiceProvider;

class EditColumnComponentServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     *
     * @return void
     */
    public function register()
    {
        $this->app->singleton('editColumn', function () {
            return new EditColumnComponent();
        });
    }

    public function provides()
    {
        return [
            'editColumn'
        ];
    }
}
