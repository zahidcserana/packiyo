<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ShippedItemTableResource extends JsonResource
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
        $resource['order_id'] = $this->orderItem->order->id;
        $resource['order_number'] = $this->orderItem->order->number;
        $resource['quantity'] = $this->quantity;
        $resource['location_name'] = is_null($this->location) ? '' : $this->location->name;
        $resource['location_id'] = is_null($this->location) ? '' : $this->location->id;
        $resource['lot_name'] = is_null($this->lot) ? '' : $this->lot->name;
        $resource['lot_expiration'] = is_null($this->lot) ? '' : $this->lot->expiration_date;
        $resource['vendor'] = is_null($this->lot) ? '' : $this->lot->supplier->contactInformation->name;
        $resource['tote_name'] = is_null($this->tote) ? '' : $this->tote->name;
        $resource['serial_number'] = $this->serial_number;

        return $resource;
    }
}
