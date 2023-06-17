<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class OrderItemTableResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  Request  $request
     * @return array
     */
    public function toArray($request): array
    {
        unset($resource);

        $resource['id'] = $this->id;
        $resource['order_number'] = $this->order->number;
        $resource['order_id'] = $this->order->id;
        $resource['ordered_at'] = user_date_time($this->ordered_at, true);
        $resource['quantity'] = $this->quantity;
        $resource['quantity_pending'] = $this->quantity_pending;
        $resource['quantity_shipped'] = $this->quantity_shipped;
        $resource['quantity_allocated'] = $this->quantity_allocated;
        $resource['quantity_backordered'] = $this->quantity_backordered;

        return $resource;
    }
}
