<?php

namespace App\Observers;

use App\Jobs\AllocateInventoryJob;
use App\Models\Location;

class LocationObserver
{
    /**
     * Handle the Location "saving" event.
     *
     * @param Location $location
     * @return void
     */
    public function saving(Location $location): void
    {
        if (is_null($location->locationType)) {
            $location->pickable_effective = $location->pickable;
            $location->disabled_on_picking_app_effective = $location->disabled_on_picking_app;
        } else {
            $location->pickable_effective = $location->locationType->pickable ?? $location->pickable;
            $location->disabled_on_picking_app_effective = $location->locationType->disabled_on_picking_app ?? $location->disabled_on_picking_app;
        }
    }

    public function saved(Location $location): void
    {
        if ($location->wasChanged('pickable_effective')) {
            $locationProducts = $location->products()->where('quantity_on_hand', '!=', 0)->get();
            foreach ($locationProducts as $locationProduct) {
                AllocateInventoryJob::dispatch($locationProduct->product);
            }
        }
    }
}
