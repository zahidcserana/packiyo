<?php

namespace App\Observers;

use App\Models\Location;
use App\Models\LocationType;

class LocationTypeObserver
{
    /**
     * Handle the Location "saving" event.
     *
     * @param LocationType $locationType
     * @return void
     */
    public function updated(LocationType $locationType): void
    {
        if ($locationType->isDirty('pickable') || $locationType->isDirty('disabled_on_picking_app')) {
            $locations = $locationType->locations;

            foreach ($locations as $location) {
                $location->update();
            }
        }
    }
}
