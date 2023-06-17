<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ShippingMethodTableResource extends JsonResource
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

        $resource['id'] = $this->id;
        $resource['name'] = $this->name;
        $resource['carrier_name'] = $this->shippingCarrier->name;
        $resource['link_edit'] = route('shipping_method.edit', [
            'shipping_method' => $this->id
        ]);

        $resource['tags'] = $this->tags->pluck('name');

        return $resource;
    }
}
