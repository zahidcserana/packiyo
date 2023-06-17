<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Support\Facades\DB;

class ReplenishmentReportTableResource extends JsonResource
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

        $resource['product_name'] = $this->name;
        $resource['product_url'] = route('product.edit', ['product' => $this]);
        $resource['sku'] = $this->sku;
        $resource['quantity_on_hand'] = $this->quantity_on_hand;
        $resource['quantity_allocated'] = $this->quantity_allocated;
        $resource['quantity_pickable'] = $this->quantity_pickable;
        $resource['qty'] = $this->quantity_to_replenish;

        return $resource;
    }
}
