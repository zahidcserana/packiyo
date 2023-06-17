<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;
use App\Models\Location;
use App\Models\PurchaseOrder;
use App\Models\Return_;
use App\Models\Shipment;

class InventoryLogResource extends JsonResource
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

        $resource['user'] = new UserResource($this->user);
        unset($resource['user_id']);

        $resource['product'] = new ProductResource($this->product);
        unset($resource['product_id']);

        $resource['location'] = new LocationResource($this->location);
        unset($resource['location_id']);

        return $resource;
    }
}
