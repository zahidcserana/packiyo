<?php

namespace App\Observers;

use App\Jobs\AllocateInventoryJob;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Product;

class OrderItemObserver
{
    /**
     * Handle the OrderItem "created" event.
     *
     * @param  OrderItem $orderItem
     * @return void
     */
    public function created(OrderItem $orderItem) : void
    {
        Order::disableAuditing();

        $orderItem->order->save();
        AllocateInventoryJob::dispatch($orderItem->product);
    }

    /**
     * Handle the OrderItem "creating" event.
     *
     * @param  OrderItem $orderItem
     * @return void
     */
    public function creating(OrderItem $orderItem) : void
    {
        /** @var Product $product */
        $product = Product::findOrFail($orderItem->product_id);

        if (empty($orderItem->name)) {
            $orderItem->name = $product->name;
        }

        if (empty($orderItem->sku)) {
            $orderItem->sku = $product->sku;
        }

        if (empty($orderItem->price)) {
            $orderItem->price = $orderItem->order_item_kit_id ? 0 : $product->price;
        }

        if (empty($orderItem->weight)) {
            $orderItem->weight = $product->weight ?? 0;
        }

        if (empty($orderItem->height)) {
            $orderItem->height = $product->height ?? 0;
        }

        if (empty($orderItem->width)) {
            $orderItem->width = $product->width ?? 0;
        }

        if (empty($orderItem->length)) {
            $orderItem->length = $product->length ?? 0;
        }

        if (empty($orderItem->ordered_at)) {
            $orderItem->ordered_at = $orderItem->order->ordered_at;
        }

        $orderItem->quantity_pending = $orderItem->quantity;
    }

    /**
     * Handle the OrderItem "updated" event.
     *
     * @param  OrderItem $orderItem
     * @return void
     */
    public function updated(OrderItem $orderItem) : void
    {
        Order::disableAuditing();

        if ($orderItem->wasChanged('quantity_pending')) {
            AllocateInventoryJob::dispatch($orderItem->product);
        }

        $orderItem->order->save();
    }

    /**
     * Handle the OrderItem "updating" event.
     *
     * @param  OrderItem $orderItem
     * @return void
     */
    public function updating(OrderItem $orderItem): void
    {
        if (!$orderItem->order->cancelled_at && !$orderItem->order->fulfilled_at && !$orderItem->cancelled_at) {
            $orderItem->quantity_pending = $orderItem->quantity - $orderItem->quantity_shipped + $orderItem->quantity_reshipped;
        }

        $orderItem->quantity_pending = max(0, $orderItem->quantity_pending);
        $orderItem->quantity_shipped = max($orderItem->quantity_shipped, $orderItem->shipmentItems()->sum('quantity'));

        if ($orderItem->cancelled_at) {
            $orderItem->quantity_pending = 0;
        }
    }

    /**
     * Handle the order item "deleted" event.
     *
     * @param  OrderItem  $orderItem
     * @return void
     */
    public function deleted(OrderItem $orderItem): void
    {
        $orderItem->order->save();

        AllocateInventoryJob::dispatch($orderItem->product);
    }
}
