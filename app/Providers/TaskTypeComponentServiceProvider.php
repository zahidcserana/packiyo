<?php

namespace App\Providers;

use App\Components\TaskTypeComponent;
use Illuminate\Support\ServiceProvider;

class TaskTypeComponentServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     *
     * @return void
     */
    public function register()
    {
        $this->app->singleton('taskType', function () {
            return new TaskTypeComponent();
        });
    }

    public function provides()
    {
        return [
            'taskType'
        ];
    }
}
