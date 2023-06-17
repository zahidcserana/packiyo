<?php

namespace App\Providers;

use App\Components\PickingBatchComponent;
use Illuminate\Support\ServiceProvider;

class PickingBatchComponentServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     *
     * @return void
     */
    public function register()
    {
        $this->app->singleton('pickingBatch', function () {
            return new PickingBatchComponent();
        });
    }

    public function provides()
    {
        return [
            'pickingBatch'
        ];
    }
}
