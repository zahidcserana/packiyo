<?php

namespace App\Observers;

use App\Models\Product;

class ProductObserver
{
    public function saved(Product $product): void
    {
        if ($product->wasChanged(['quantity_on_hand', 'quantity_available', 'quantity_backordered'])) {
            app('inventoryLog')->triggerAdjustInventoryWebhook($product);
        }
    }
}
