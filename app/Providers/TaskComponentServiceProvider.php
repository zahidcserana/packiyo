<?php

namespace App\Providers;

use App\Components\TaskComponent;
use Illuminate\Support\ServiceProvider;

class TaskComponentServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     *
     * @return void
     */
    public function register()
    {
        $this->app->singleton('task', function () {
            return new TaskComponent();
        });
    }

    public function provides()
    {
        return [
            'task'
        ];
    }
}
