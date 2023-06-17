<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class LocationTypeTableResource extends JsonResource
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
        $resource['pickable'] = is_null($this->pickable) ? 'Not set' : ($this->pickable === 1 ? 'Yes' : 'No');
        $resource['disabled_on_picking_app'] = is_null($this->disabled_on_picking_app) ? 'Not set' : ($this->disabled_on_picking_app === 1 ? 'Yes' : 'No');
        $resource['sellable'] = is_null($this->sellable) ? 'Not set' : ($this->sellable === 1 ? 'Yes' : 'No');
        $resource['customer'] = ['name' => $this->customer->contactInformation->name, 'url' => route('customer.edit', ['customer' => $this->customer])];
        $resource['link_edit'] = route('location_type.edit', ['location_type' => $this]);
        $resource['link_delete'] = ['token' => csrf_token(), 'url' => route('location_type.destroy', ['id' => $this->id, 'location_type' => $this])];

        return $resource;
    }
}
