<?php

namespace App\Observers;

use App\Models\Location;
use App\Models\Warehouse;
use Illuminate\Support\Carbon;

class WarehouseObserver
{
    /**
     * Handle the warehouse "saved" event.
     *
     * @param Warehouse $warehouse
     * @return void
     */
    public function saved(Warehouse $warehouse): void
    {
        $receivingLocation = Location::firstOrCreate([
            'warehouse_id' => $warehouse->id,
            'name' => Location::PROTECTED_LOC_NAME_RECEIVING,
            'protected' => true
        ]);

        $reshipLocation = Location::firstOrCreate([
            'warehouse_id' => $warehouse->id,
            'name' => Location::PROTECTED_LOC_NAME_RESHIP,
            'protected' => true
        ]);
    }
}
