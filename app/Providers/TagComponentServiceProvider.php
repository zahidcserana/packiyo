<?php

namespace App\Providers;

use App\Components\TagComponent;
use Illuminate\Support\ServiceProvider;

class TagComponentServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     *
     * @return void
     */
    public function register()
    {
        $this->app->singleton('tag', function (){
            return new TagComponent();
        });
    }

    public function provides()
    {
        return [
            'tag'
        ];
    }
}
