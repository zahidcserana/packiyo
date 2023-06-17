<?php

namespace App\Components;

use App\Http\Requests\CycleCountBatch\CloseCountingTaskRequest;
use App\Http\Requests\CycleCountBatch\CountRequest;
use App\Http\Requests\CycleCountBatch\CycleCountBatchRequest;
use App\Http\Requests\CycleCountBatch\PickRequest;
use App\Models\Customer;
use App\Models\CycleCountBatch;
use App\Models\CycleCountBatchItem;
use App\Models\Location;
use App\Models\LocationProduct;
use App\Models\Product;
use App\Models\Task;
use App\Models\TaskType;
use Carbon\Carbon;

class CycleCountBatchComponent extends BaseComponent
{
    public function availableBatch(CycleCountBatchRequest $request)
    {
        $user = auth()->user();

        $customer = Customer::where('id', $request['customer_id'])->first();
        $quantity = $request['quantity'];
        $type = $request['type'];

        $cycleCountBatch = [];

        if ($type !== TaskType::TYPE_COUNTING_LOCATIONS) {
            $type = TaskType::TYPE_COUNTING_PRODUCTS;
        }

        $taskType = $this->getTaskType($customer, $type);

        if (!$taskType) {
            return $cycleCountBatch;
        }

        $customerIds = Customer::where('id', $request['customer_id'])->orWhere('parent_id', $request['customer_id'])->pluck('id')->toArray();

        if ($quantity) {
            if ($type === TaskType::TYPE_COUNTING_LOCATIONS) {
                if(count($models = Location::orderByRaw('-priority_counting_requested_at DESC')->orderByRaw('-last_counted_at DESC')->whereHas('products')->whereHas('warehouse', function($warehouse) use ($customerIds) {
                    $warehouse->whereIn('customer_id', $customerIds);
                })->limit($quantity)->get())){
                    $cycleCountBatch = $this->createLocationsBatch($models, $user, $type);
                }
            } else {
                if(count($models = Product::orderByRaw('-priority_counting_requested_at DESC')->orderByRaw('-last_counted_at DESC')->whereHas('locations')->whereIn('customer_id', $customerIds)->limit($quantity)->get())){
                    $cycleCountBatch = $this->createProductsBatch($models, $user, $type);
                }
            }

            if ($cycleCountBatch) {
                $this->createTask($cycleCountBatch, $taskType, $customer, $user);
            }
        } else {
            if ($task = Task::where('taskable_type', CycleCountBatch::class)->where('user_id', $user->id)->where('completed_at', null)->first()) {
                $cycleCountBatch = CycleCountBatch::find($task->taskable_id);
            }
        }

        return $cycleCountBatch;
    }

    public function closeCountingTask(CloseCountingTaskRequest $request)
    {
        $user = auth()->user();

        $customer = Customer::where('id', $request['customer_id'])->first();
        $type = $request['type'];
        $tasks = [];

        if ($type !== TaskType::TYPE_COUNTING_LOCATIONS) {
            $type = TaskType::TYPE_COUNTING_PRODUCTS;
        }

        $taskType = $this->getTaskType($customer, $type);

        if (!$taskType) {
            return $tasks;
        }

        $tasks = Task::where('taskable_type', CycleCountBatch::class)->where('user_id', $user->id)->where('completed_at', null)->get();

        foreach ($tasks as $task) {
            $cycleCountBatch = CycleCountBatch::find($task->taskable_id);

            foreach ($cycleCountBatch->cycleCountBatchItems as $cycleCountBatchItem) {
                if ($type === TaskType::TYPE_COUNTING_LOCATIONS) {
                    $location = Location::where('id', $cycleCountBatchItem->location_id)->first();

                    if ($cycleCountBatchItem->quantity_confirmed !== null) {
                        $location->last_counted_at = Carbon::now();
                    } else {
                        $location->priority_counting_requested_at = null;
                    }

                    $location->save();
                } else {
                    $product = Product::where('id', $cycleCountBatchItem->product_id)->first();

                    if ($cycleCountBatchItem->quantity_confirmed !== null) {
                        $product->last_counted_at = Carbon::now();
                    } else {
                        $product->priority_counting_requested_at = null;
                    }

                    $product->save();
                }
            }

            $task->completed_at = Carbon::now();
            $task->save();
        }

        return $tasks;
    }

    public function pick(PickRequest $request)
    {
        $cycleCountBatchItem = CycleCountBatchItem::find($request['cycle_count_batch_item_id']);

        $location = LocationProduct::where('product_id', $cycleCountBatchItem->product_id)->where('location_id', $cycleCountBatchItem->location_id)->first();
        $cycleCountBatchItem->quantity = $location->quantity_on_hand;
        $cycleCountBatchItem->save();

        $cycleCountBatch = $cycleCountBatchItem->cycleCountBatch;

        return $cycleCountBatch;
    }

    public function count(CountRequest $request)
    {
        $cycleCountBatchItem = CycleCountBatchItem::find($request['cycle_count_batch_item_id']);
        $cycleCountBatchItem->quantity_confirmed = $request['quantity'];
        $cycleCountBatchItem->save();

        $type = $request['type'];

        $location = Location::find($request['location_id']);
        $product = $cycleCountBatchItem->product;

        app('inventoryLog')->adjustInventory(
            $location,
            $product,
            $request['quantity'],
            InventoryLogComponent::OPERATION_TYPE_CYCLE_COUNT
        );

        $cycleCountBatch = $cycleCountBatchItem->CycleCountBatch;
        $cycleCountBatchItems = $cycleCountBatch->CycleCountBatchItems;
        $taskCompleted = true;

        foreach ($cycleCountBatchItems as $cycleCountBatchItem) {
            if (is_null($cycleCountBatchItem->quantity_confirmed)) {
                $taskCompleted = false;
            }
        }

        if ($taskCompleted) {
            $task = Task::where('taskable_id', $cycleCountBatch->id)->first();
            $task->completed_at = Carbon::now();
            $task->save();

            if ($type === TaskType::TYPE_COUNTING_LOCATIONS) {
                $location->priority_counting_requested_at = null;
                $location->last_counted_at = Carbon::now();
                $location->save();
            } else {
                $product->priority_counting_requested_at = null;
                $product->last_counted_at = Carbon::now();
                $product->save();
            }
        }

        return $cycleCountBatch;
    }

    public function createLocationsBatch ($locations, $user, $type) {
        $cycleCountBatch = CycleCountBatch::create([
            'user_id' => $user->id,
            'type' => $type
        ]);

        foreach ($locations as $location) {
            foreach ($location->products as $locationProduct) {
                CycleCountBatchItem::create([
                    'cycle_count_batch_id' => $cycleCountBatch->id,
                    'product_id' => $locationProduct->product->id,
                    'location_id' => $location->id,
                ]);
            }
        }

        return $cycleCountBatch;
    }

    public function createProductsBatch ($products, $user, $type) {
        $cycleCountBatch = CycleCountBatch::create([
            'user_id' => $user->id,
            'type' => $type
        ]);

        foreach ($products as $product) {
            foreach ($product->locations as $location) {
                CycleCountBatchItem::create([
                    'cycle_count_batch_id' => $cycleCountBatch->id,
                    'product_id' => $product->id,
                    'location_id' => $location->id
                ]);
            }
        }

        return $cycleCountBatch;
    }

    public function getTaskType(Customer $customer, $type)
    {
        return TaskType::where('customer_id', $customer->id)->where('type', $type)->first();
    }

    public function createTask(CycleCountBatch $cycleCountBatch, TaskType $taskType, Customer $customer, $user)
    {
        $task = new Task();
        $task->taskable()->associate($cycleCountBatch);
        $task->user_id = $user->id;
        $task->customer_id = $customer->id;
        $task->task_type_id = $taskType->id;
        $task->notes = '';
        $task->save();
    }
}
