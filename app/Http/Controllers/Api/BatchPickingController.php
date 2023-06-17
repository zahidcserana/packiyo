<?php

namespace App\Http\Controllers\Api;

use App\Http\Requests\PickingBatch\ClosePickingTaskRequest;
use App\Http\Requests\PickingBatch\ExistingItemRequest;
use App\Http\Requests\PickingBatch\MultiOrderRequest;
use App\Http\Requests\PickingBatch\PickingBatchRequest;
use App\Http\Requests\PickingBatch\PickRequest;
use App\Http\Requests\PickingBatch\SingleItemBatchRequest;
use App\Http\Requests\PickingBatch\SingleOrderRequest;
use App\JsonApi\V1\PickingBatches\PickingBatchSchema;
use App\Models\Customer;
use App\Models\Location;
use App\Models\PickingBatch;
use App\Models\Order;
use App\Models\OrderLock;
use App\Http\Controllers\ApiController;
use App\Models\PickingBatchItem;
use App\Models\Task;
use App\Models\Tote;
use App\Models\UserSetting;
use Carbon\Carbon;
use Illuminate\Support\Facades\Log;
use LaravelJsonApi\Core\Responses\DataResponse;
use LaravelJsonApi\Laravel\Http\Requests\AnonymousCollectionQuery;

/**
 * Class BatchPickingController
 * @package App\Http\Controllers\Api
 * @group Batch Picking
 */
class BatchPickingController extends ApiController
{
    /**
     * @param PickingBatchSchema $schema
     * @param AnonymousCollectionQuery $collectionQuery
     * @param PickingBatchRequest $request
     * @return DataResponse
     */
    public function pickingBatches(PickingBatchSchema $schema, AnonymousCollectionQuery $collectionQuery, PickingBatchRequest $request): DataResponse
    {
        $user = auth()->user();

        $customerIds = Customer::where('id', $request->customer_id)->orWhere('parent_id', $request->customer_id)->pluck('id')->toArray();

        $orders = Order::whereDoesntHave('orderLock')
            ->whereIntegerInRaw('customer_id', $customerIds)
            ->where('ready_to_pick', '1')
            ->whereNull('fulfilled_at')
            ->orderBy('priority', 'desc')
            ->orderBy('ordered_at')
            ->get();

        $models = $schema
            ->repository()
            ->queryAll()
            ->withRequest($collectionQuery)
            ->firstOrPaginate($collectionQuery->page());

        $orderIds = (app('routeOptimizer')->createPickingBatch($request, $orders, $user)->pluck('id'));

        $models = $models->whereIntegerInRaw('id', $orderIds);

        return new DataResponse($models);
    }

    public function singleItemBatchPicking(PickingBatchSchema $schema, AnonymousCollectionQuery $collectionQuery, SingleItemBatchRequest $request): DataResponse
    {
        $user = auth()->user();

        $pickingBatch = null;
        $tasks = Task::where('user_id', $user->id)->where('taskable_type', PickingBatch::class)->where('completed_at', null)->get();

        foreach ($tasks as $task) {
            $taskPickingBatch = PickingBatch::find($task->taskable_id);

            if ($taskPickingBatch) {
                if ($taskPickingBatch->type === "sib") {
                    $pickingBatch = $taskPickingBatch;

                    break;
                }
            }
        }

        if (!$pickingBatch) {
            $customerIds = Customer::where('id', $request->customer_id)->orWhere('parent_id', $request->customer_id)->pluck('id')->toArray();
            $orders = Order::whereDoesntHave('orderLock')
                ->whereIntegerInRaw('customer_id', $customerIds)
                ->where('ready_to_pick', '1')
                ->whereHas('orderItems')
                ->withCount('orderItems')
                ->having('order_items_count', 1)
                ->whereDoesntHave('orderItems.placedToteOrderItems')
                ->with(['orderItems.pickingBatchItems', 'orderItems.product.locations'])
                ->orderBy('priority', 'desc')
                ->orderBy('ordered_at');

            if (isset($request->tag_id)) {
                $orders = $orders->whereHas('tags', function ($query) use ($request) {
                    $query->where('id', '=', $request->tag_id);
                })
                ->withCount('tags');
            }

            $orders = $orders->get();

            if (count($orders)) {
                $pickingBatch = app('routeOptimizer')->createPickingBatch($request, $orders, $user, "sib");
            }
        }

        return new DataResponse($pickingBatch);
    }

    public function multiOrderPicking(PickingBatchSchema $schema, AnonymousCollectionQuery $collectionQuery, MultiOrderRequest $request): DataResponse
    {
        $user = auth()->user();

        $pickingBatch = null;
        $tasks = Task::where('user_id', $user->id)->where('taskable_type', PickingBatch::class)->where('completed_at', null)->get();

        foreach ($tasks as $task) {
            $taskPickingBatch = PickingBatch::find($task->taskable_id);

            if ($taskPickingBatch) {
                if ($taskPickingBatch->type === "mib") {
                    $pickingBatch = $taskPickingBatch;

                    break;
                }
            }
        }

        if (!$pickingBatch) {
            $customerIds = Customer::where('id', $request->customer_id)->orWhere('parent_id', $request->customer_id)->pluck('id')->toArray();

            $orders = Order::whereDoesntHave('orderLock')
                ->whereIntegerInRaw('customer_id', $customerIds)
                ->where('ready_to_pick', '1')
                ->whereHas('orderItems')
                ->withCount('orderItems')
                ->whereDoesntHave('orderItems.placedToteOrderItems')
                ->whereNull('fulfilled_at')
                ->orderBy('priority', 'desc')
                ->orderBy('ordered_at');

            if (user_settings(UserSetting::USER_SETTING_EXCLUDE_SINGLE_LINE_ORDERS)) {
                $orders = $orders->having('order_items_count', '>', 1);
            }

            if (isset($request->tag_id)) {
                $orders = $orders->whereHas('tags', function ($query) use ($request) {
                    $query->where('id', '=', $request->tag_id);
                })
                ->withCount('tags');
            }

            $orders = $orders->get();

            if (count($orders)) {
                $pickingBatch = app('routeOptimizer')->createPickingBatch($request, collect($orders), $user, "mib");
            }
        }

        return new DataResponse($pickingBatch);
    }

    public function singleOrderPicking(PickingBatchSchema $schema, AnonymousCollectionQuery $collectionQuery, SingleOrderRequest $request): DataResponse
    {
        $user = auth()->user();

        $pickingBatch = null;

        $orderLockIds = OrderLock::get()->where('user_id', '=', $user->id)->pluck('id')->toArray();
        $order = Order::with('orderItems.pickingBatchItems.pickingBatch')->where('ready_to_pick', '1')->where(function ($query) use ($orderLockIds) {
            $query->whereDoesntHave('orderLock')->orWhereHas('orderLock', function ($query) use ($orderLockIds) {
                $query->whereIn('id', $orderLockIds);
            });
        })->find($request->order_id);

        if ($order) {
            foreach ($order->orderItems as $orderItem) {
                $pickingBatches = $orderItem->pickingBatchItems->first();

                if ($pickingBatches) {
                    $pickingBatch = $pickingBatches->pickingBatch;
                    break;
                }
            }

            if ($pickingBatch) {
                if ($pickingBatch->type !== "so") {
                    $pickingBatch = null;
                }
            } else {
                $pickingBatch = app('routeOptimizer')->createPickingBatch($request, collect([$order]), $user, "so");
            }
        }

        return new DataResponse($pickingBatch);
    }

    public function existingItems(PickingBatchSchema $schema, AnonymousCollectionQuery $collectionQuery, ExistingItemRequest $request): DataResponse
    {
        $user = auth()->user();
        $customerIds = Customer::where('id', $request->customer_id)->orWhere('parent_id', $request->customer_id)->pluck('id')->toArray();
        $tasks = Task::where('user_id', $user->id)->whereIntegerInRaw('customer_id', $customerIds)->where('taskable_type', PickingBatch::class)->where('completed_at', null)->get();
        $pickingBatch = null;

        switch ($request->type) {
            case "so":
                $order = Order::find($request->order_id);

                if ($order) {
                    $orderItems = $order->orderItems;

                    foreach ($orderItems as $orderItem) {
                        $pickingBatches = $orderItem->pickingBatchItems->first();

                        if ($pickingBatches) {
                            $pickingBatch = $pickingBatches->pickingBatch;

                            break;
                        }
                    }
                }
                break;
            case "sib":
                foreach ($tasks as $task) {
                    $taskPickingBatch = PickingBatch::find($task->taskable_id);

                    if ($taskPickingBatch) {
                        if ($taskPickingBatch->type === "sib") {
                            $pickingBatch = $taskPickingBatch;

                            break;
                        }
                    }
                }
                break;
            case "mib":
                foreach ($tasks as $task) {
                    $taskPickingBatch = PickingBatch::find($task->taskable_id);

                    if ($taskPickingBatch) {
                        if ($taskPickingBatch->type === "mib") {
                            $pickingBatch = $taskPickingBatch;

                            break;
                        }
                    }
                }
                break;
        }

        return new DataResponse($pickingBatch);
    }


    public function closePickingTask(PickingBatchSchema $schema, AnonymousCollectionQuery $collectionQuery, ClosePickingTaskRequest $request): DataResponse
    {
        $pickingBatch = PickingBatch::find($request->picking_batch_id);

        $task = app('pickingBatch')->closePickingTask($pickingBatch);

        return new DataResponse($task);
    }

    /**
     * @param PickingBatchSchema $schema
     * @param AnonymousCollectionQuery $collectionQuery
     * @param PickRequest $request
     * @param PickingBatch $pickingBatch
     * @return DataResponse
     */
    public function pick(PickingBatchSchema $schema, AnonymousCollectionQuery $collectionQuery, PickRequest $request): DataResponse
    {
        return new DataResponse(app()->routeOptimizer->pick($request));
    }
}
