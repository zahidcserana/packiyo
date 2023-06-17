<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class PurchaseOrderItemResource extends JsonResource
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

        $resource['product'] = new ProductResource($this->product);

        $resource['location'] = new LocationCollection($this->product->locations);

        unset($resource['product']['location']);
        unset($resource['product_id']);

        return $resource;
    }
}
