<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ToteOrderItemTableResource extends JsonResource
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
        $resource['quantity'] = $this->quantity;
        $resource['quantity_removed'] = $this->quantity_removed;
        $resource['quantity_remaining'] = $this->quantity_remaining;
        $resource['order'] = [
            'id' => $this->orderItem->order->id,
            'number' => $this->orderItem->order->number,
            'url' => route('order.edit', ['order' => $this->orderItem->order]),
        ];
        $resource['product'] = [
            'id' => $this->orderItem->product->id,
            'sku' => $this->orderItem->product->sku,
            'url' => route('product.edit', ['product' => $this->orderItem->product]),
        ];
        $resource['tote'] = [
            'id' => $this->tote->id,
            'name' => $this->tote->name,
            'url' => route('tote.edit', ['tote' => $this->tote]),
        ];
        $resource['created_at'] = user_date_time($this->created_at, true);
        $resource['updated_at'] = user_date_time($this->updated_at, true);
        $resource['picked_by'] = $this->user->contactInformation->name ?? '';

        return $resource;
    }
}
