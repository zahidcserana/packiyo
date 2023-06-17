<?php

namespace App\Providers;

use App\Components\ShipmentComponent;
use App\Models\Order;
use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\Validator;
use App\Models\OrderItem;
use App\Models\LocationProduct;

class ShipmentComponentServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     *
     * @return void
     */
    public function boot()
    {
        Validator::extend('validate_order_item', function ($attribute, $value, $parameters, $validator) {
            $order = $this->app->request->route('order');

            if (!$order && $orderId = $this->app->request->input('order_id')) {
                $order = Order::find($orderId);
            }

            if ($bulkShipBatch = $this->app->request->route('bulkShipBatch')) {
                $orderItemIds = OrderItem::whereIn('order_id', $bulkShipBatch->orders->pluck('id')->toArray())
                    ->get()
                    ->modelKeys();
            } else {
                $orderItemIds = $order->orderItems->pluck('id')->toArray();
            }

            return in_array($value, $orderItemIds);
        });
    }

    public function register()
    {
        $this->app->singleton('shipment', function () {
            return new ShipmentComponent();
        });
    }

    public function provides()
    {
        return [
            'shipment'
        ];
    }
}
