<?php

namespace App\Observers;

use App\Jobs\AllocateInventoryJob;
use App\Models\PurchaseOrder;
use App\Models\PurchaseOrderItem;
use App\Models\PurchaseOrderStatus;
use Carbon\Carbon;

class PurchaseOrderItemObserver
{
    /**
     * Handle the PurchaseOrderItem "saving" event.
     *
     * @param PurchaseOrderItem $purchaseOrderItem
     * @return void
     */
    public function saving(PurchaseOrderItem $purchaseOrderItem): void
    {
        $purchaseOrderItem->quantity_pending = $purchaseOrderItem->quantity - $purchaseOrderItem->quantity_received - $purchaseOrderItem->quantity_rejected;

        AllocateInventoryJob::dispatch($purchaseOrderItem->product);
    }

    /**
     * Handle the PurchaseOrderItem "saved" event.
     *
     * @param PurchaseOrderItem $purchaseOrderItem
     * @return void
     */
    public function saved(PurchaseOrderItem $purchaseOrderItem): void
    {
        if (is_null($purchaseOrderItem->purchaseOrder->received_at)) {
            $purchaseOrder = $purchaseOrderItem->purchaseOrder;

            $pending = false;

            foreach ($purchaseOrder->purchaseOrderItems as $item) {
                if ($item->quantity_pending > 0 || $item->quantity_rejected > 0 ) {
                    $pending = true;
                }
            }

            if (!$pending) {
                $purchaseOrder->received_at = Carbon::now();
                $purchaseOrder->save();
            }
        }
    }
}
