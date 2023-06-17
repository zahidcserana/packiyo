<?php

namespace App\Providers;

use App\Components\BulkShipComponent;
use Illuminate\Support\ServiceProvider;

class BulkShipComponentServiceProvide extends ServiceProvider
{
    /**
     * Register services.
     *
     * @return void
     */
    public function register(): void
    {
        $this->app->singleton('bulkShip', function (){
            return new BulkShipComponent();
        });
    }

    public function provides(): array
    {
        return [
            'bulkShip',
        ];
    }
}
