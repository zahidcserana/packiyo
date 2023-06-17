<?php

namespace App\Observers;

use App\Models\Order;

class OrderObserver
{
    /**
     * Handle the order "saving" event.
     *
     * @param Order $order
     * @return void
     */
    public function saving(Order $order): void
    {
        $order->recalculateWeight();

        app('order')->updatePriorityScore($order);
        app('order')->recalculateStatus($order);
        app('order')->recalculateTotals($order);
    }

    public function saved(Order $order): void
    {
        Order::enableAuditing();

        app('order')->updateSummedQuantities([$order->id]);
        app('bulkShip')->syncSingleOrder($order);
    }
}
