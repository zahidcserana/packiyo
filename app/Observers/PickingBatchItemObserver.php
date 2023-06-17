<?php

namespace App\Observers;

use App\Models\LocationProduct;
use App\Models\PickingBatchItem;

class PickingBatchItemObserver
{
    /**
     * Handle the PickingBatchItem "saved" event.
     *
     * @param  \App\Models\PickingBatchItem  $pickingBatchItem
     * @return void
     */
    public function saved(PickingBatchItem $pickingBatchItem)
    {
        $locationProduct = LocationProduct::where('location_id', $pickingBatchItem->location_id)
            ->where('product_id', $pickingBatchItem->orderItem->product_id)
            ->first();

        $locationProduct->calculateQuantityReservedForPicking();
    }

    /**
     * Handle the PickingBatchItem "deleted" event.
     *
     * @param  \App\Models\PickingBatchItem  $pickingBatchItem
     * @return void
     */
    public function deleted(PickingBatchItem $pickingBatchItem)
    {
        $this->saved($pickingBatchItem);
    }
}
