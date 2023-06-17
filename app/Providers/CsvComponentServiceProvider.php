<?php

namespace App\Providers;

use App\Components\CSVComponent;
use Illuminate\Support\ServiceProvider;

class CsvComponentServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     *
     * @return void
     */
    public function register(): void
    {
        $this->app->singleton('csv', function (){
            return new CSVComponent();
        });
    }

    public function provides(): array
    {
        return [
            'csv'
        ];
    }
}
