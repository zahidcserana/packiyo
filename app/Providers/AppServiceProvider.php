<?php

namespace App\Providers;

use App\Http\Routing\ResourceRegistrar;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot()
    {
        if (config('app.env') == 'local') {
            DB::listen(function($query) {
                Log::debug($query->sql, [$query->bindings, $query->time]);
            });
        }
    }

    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
        $resourceRegistrar = new ResourceRegistrar($this->app['router']);

        $this->app->bind(\Illuminate\Routing\ResourceRegistrar::class, function() use ($resourceRegistrar) {
            return $resourceRegistrar;
        });
    }
}
