<?php

namespace App\Providers;

use App\Components\CycleCountBatchComponent;
use Illuminate\Support\ServiceProvider;

class CycleCountBatchComponentServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     *
     * @return void
     */
    public function register()
    {
        $this->app->singleton('cycleCountBatch', function () {
            return new CycleCountBatchComponent();
        });
    }

    public function provides()
    {
        return [
            'cycleCountBatch'
        ];
    }
}
