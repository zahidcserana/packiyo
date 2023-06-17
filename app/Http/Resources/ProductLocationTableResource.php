<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ProductLocationTableResource extends JsonResource
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
        $resource['product_id'] = $this->product->id;
        $resource['location_id'] = $this->location->id;
        $resource['location_product_id'] = $this->location_product_id;
        $resource['location'] = $this->location->name;
        $resource['warehouse'] = $this->location->warehouse->contactInformation['name'];
        $resource['sku'] = $this->product->sku;
        $resource['product_name'] = $this->product->name;
        $resource['quantity'] = $this->product
            ->locations()
            ->where('location_id', $this->location->id)
            ->first()
            ->pivot
            ->quantity_on_hand;
        $resource['location_pickable'] = $this->location->isPickableLabel();
        $resource['location_sellable'] = $this->location->isSellableLabel();

        return $resource;
    }
}
