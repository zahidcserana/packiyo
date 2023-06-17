<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class ShippingBoxTableResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array
     */
    public function toArray($request)
    {
        unset($resource);

        $resource['id'] = $this->id;
        $resource['name'] = $this->name;
        $resource['length'] = $this->length.($this->length_locked == 1 ? ' '.__('locked') : '');
        $resource['width'] = $this->width.($this->width_locked == 1 ? ' '.__('locked') : '');
        $resource['height'] = $this->height.($this->height_locked == 1 ? ' '.__('locked') : '');
        $resource['customer'] = ['name' => $this->customer->contactInformation->name, 'url' => route('customer.edit', ['customer' => $this->customer])];
        $resource['link_edit'] = route('shipping_box.edit', [ 'shipping_box' => $this ]);
        $resource['link_delete'] = ['token' => csrf_token(), 'url' => route('shipping_box.destroy', ['id' => $this->id, 'shipping_box' => $this])];

        return $resource;
    }
}
