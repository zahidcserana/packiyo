<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class PickingBatchResource extends JsonResource
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

        $resource['picking_batch_items'] = new PickingBatchItemCollection($this->pickingBatchItems);

        $resource['customer'] = new CustomerResource($this->customer);
        unset($resource['customer_id']);

        return $resource;
    }
}
