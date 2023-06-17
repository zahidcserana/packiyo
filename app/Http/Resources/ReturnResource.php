<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class ReturnResource extends JsonResource
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

        $resource['order'] = new OrderResource($this->order);
        unset($resource['order_id']);

        $resource['return_items'] = new ReturnItemCollection($this->returnItems);

        return $resource;
    }
}
