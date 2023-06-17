<?php

namespace App\Providers;

use App\Components\RouteOptimizationComponent;
use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\Validator;
use App\Models\Order;
use App\Models\OrderLock;
use App\Models\TaskType;

class RouteOptimizationComponentServiceProvider extends ServiceProvider
{
    public function boot()
    {
        Validator::extend('check_orders', function ($attribute, $value, $parameters, $validator) {
            $orderLockIds = OrderLock::get()->pluck('order_id')->toArray();

            $orders = Order::whereNotIn('id', $orderLockIds)->where('customer_id', $value)->get();

            if (count($orders) == 0) {
                return false;
            }

            return true;
        });

        Validator::extend('check_task_type', function ($attribute, $value, $parameters, $validator) {
            $taskType = TaskType::where('customer_id', $value)->where('type', TaskType::TYPE_PICKING)->first();

            if (is_null($taskType)) {
                return false;
            }

            return true;
        });

        Validator::extend('validate_picking_batch_item', function ($attribute, $value, $parameters, $validator) {
            $pickingBatch = $this->app->request->route('picking_batch');

            $pickingBatchItemIds = $pickingBatch->pickingBatchItems->pluck('id')->toArray();

            return in_array($value, $pickingBatchItemIds);
        });
    }

    /**
     * Register services.
     *
     * @return void
     */
    public function register()
    {
        $this->app->singleton('routeOptimizer', function () {
            return new RouteOptimizationComponent();
        });
    }

    public function provides()
    {
        return [
            'routeOptimizer'
        ];
    }
}
