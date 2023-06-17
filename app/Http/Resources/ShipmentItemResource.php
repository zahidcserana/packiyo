<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class ShipmentItemResource extends JsonResource
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

        $resource['order_item'] = (new OrderItemResource($this->orderItem))->toArray($request);
        unset($resource['order_item']['product']);
        unset($resource['order_item']['location']);

        unset($resource['order_item_id']);

        return $resource;
    }
}
