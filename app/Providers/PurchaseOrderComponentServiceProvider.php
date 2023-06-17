<?php

namespace App\Providers;

use App\Components\PurchaseOrderComponent;
use App\Models\PurchaseOrder;
use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\Validator;
use App\Models\PurchaseOrderItem;
use App\Models\LocationProduct;

class PurchaseOrderComponentServiceProvider extends ServiceProvider
{
    public function boot()
    {
        Validator::extend('validate_purchase_order_item', function ($attribute, $value, $parameters, $validator) {
            $purchase_order = $this->app->request->route('purchase_order');

            if (is_null($purchase_order)) {
                $purchaseOrderItem = PurchaseOrderItem::where('id', $value)->first();
                $purchase_order = $purchaseOrderItem->purchaseOrder;
            }

            $purchaseOrderItemIds = $purchase_order->purchaseOrderItems->pluck('id')->toArray();

            return in_array($value, $purchaseOrderItemIds);
        });
    }

    /**
     * Register services.
     *
     * @return void
     */
    public function register()
    {
        $this->app->singleton('purchaseOrder', function () {
            return new PurchaseOrderComponent();
        });
    }

    public function provides()
    {
        return [
            'purchaseOrder'
        ];
    }
}
