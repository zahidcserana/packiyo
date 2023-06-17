<?php

namespace App\Observers;

use App\Models\LocationProduct;
use App\Models\PickingBatchItem;
use App\Models\ToteOrderItem;

class ToteOrderItemObserver
{
    /**
     * Handle the ToteOrderItem "saving" event.
     *
     * @param  \App\Models\ToteOrderItem  $toteOrderItem
     * @return void
     */
    public function saving(ToteOrderItem $toteOrderItem)
    {
        if (is_null($toteOrderItem->quantity_remaining) && $toteOrderItem->quantity) {
            $toteOrderItem->quantity_remaining = $toteOrderItem->quantity;
        }
    }

    /**
     * Handle the ToteOrderItem "saved" event.
     *
     * @param  \App\Models\ToteOrderItem  $toteOrderItem
     * @return void
     */
    public function saved(ToteOrderItem $toteOrderItem)
    {
        $pickingBatchItem = PickingBatchItem::with('orderItem')->where('id', $toteOrderItem->picking_batch_item_id)->first();

        $pickingBatchItem->save();
    }

    /**
     * Handle the ToteOrderItem "deleted" event.
     *
     * @param  \App\Models\ToteOrderItem  $toteOrderItem
     * @return void
     */
    public function deleted(ToteOrderItem $toteOrderItem)
    {
        $this->saved($toteOrderItem);
    }
}
