<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class PickingCartTableResource extends JsonResource
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
        $resource['name'] = $this->name;
        $resource['barcode'] = $this->barcode;
        $resource['number_of_totes'] = $this->number_of_totes;
        $resource['warehouse'] = $this->warehouse->contactInformation->name;
        $resource['link_edit'] = route('picking_carts.edit', ['picking_cart' => $this]);
        $resource['link_delete'] = ['token' => csrf_token(), 'url' => route('picking_carts.destroy', ['picking_cart' => $this, 'id' => $this->id])];
        $resource['link_print_barcode'] = route('pickingCart.barcode', ['picking_cart' => $this]);

        return $resource;
    }
}
