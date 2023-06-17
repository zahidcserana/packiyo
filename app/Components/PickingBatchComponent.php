<?php

namespace App\Components;

use App\Models\PickingBatch;
use App\Models\OrderLock;
use App\Models\Task;
use Carbon\Carbon;

class PickingBatchComponent extends BaseComponent
{
    public function closePickingTask(PickingBatch $pickingBatch)
    {
        $user = auth()->user();
        $task = Task::where('taskable_id', $pickingBatch->id)->where('user_id', $user->id)->where('taskable_type', PickingBatch::class)->where('completed_at', null)->first();

        if ($task) {
            $task->completed_at = Carbon::now();
            $task->save();
        }

        $pickingBatch = PickingBatch::with('pickingBatchItems.orderItem')->find($pickingBatch->id);
        $orderIds = [];

        foreach ($pickingBatch->pickingBatchItems as $pickingBatchItem) {
            if (!in_array($pickingBatchItem->orderItem->order_id, $orderIds)) {
                $orderIds[] = $pickingBatchItem->orderItem->order_id;
            }

            if (!$pickingBatchItem->quantity_picked) {
                $pickingBatchItem->delete();
            }
        }

        $pickingBatch->delete();

        OrderLock::whereIntegerInRaw('order_id', $orderIds)->delete();

        return $task;
    }
}
