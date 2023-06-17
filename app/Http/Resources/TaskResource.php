<?php

namespace App\Http\Resources;

use App\Models\CycleCountBatch;
use Illuminate\Http\Resources\Json\JsonResource;
use App\Models\PurchaseOrder;
use App\Models\Return_;
use App\Models\PickingBatch;

class TaskResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array
     */
    public function toArray($request)
    {
        $resource = parent::toArray($request);

        $resource['user'] = new UserResource($this->user);
        unset($resource['user_id']);

        $resource['customer'] = new CustomerResource($this->customer);
        unset($resource['customer_id']);

        $resource['task_type'] = new TaskTypeResource($this->taskType);
        unset($resource['task_type_id']);

        if (isset($resource['taskable_type'])) {
            switch ($resource['taskable_type']) {
                case CycleCountBatch::class:
                    $resource['taskable'] = new CycleCountBatchResource($this->taskable);
                    break;

                case PickingBatch::class:
                    $resource['taskable'] = new PickingBatchResource($this->taskable);
                    break;

                case PurchaseOrder::class:
                    $resource['taskable'] = new PurchaseOrderResource($this->taskable);
                    break;

                case Return_::class:
                    $resource['taskable'] = new ReturnResource($this->taskable);
                    break;
            }

            unset($resource['taskable_id']);
        } else {
            $resource['taskable_type'] = null;
            $resource['taskable'] = null;

            unset($resource['taskable_id']);
        }

        return $resource;
    }
}
