<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class SerialNumberReportTableResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  Request  $request
     * @return array
     */
    public function toArray($request)
    {
        unset($resource);

        $resource['serial_number'] = $this->serial_number;
        $resource['date_shipped'] = user_date_time($this->package->shipment->created_at, true);

        $resource['order'] = [
            'id' => $this->orderItem->order->id,
            'number' => $this->orderItem->order->number,
            'url' => route('order.edit', ['order' => $this->orderItem->order]),
        ];

        $resource['product'] = [
            'id' => $this->orderItem->product->id,
            'sku' => $this->orderItem->product->sku,
            'name' => $this->orderItem->product->name,
            'url' => route('product.edit', ['product' => $this->orderItem->product]),
        ];

        return $resource;
    }
}
