<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class WarehouseTableResource extends JsonResource
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
        $resource['customer'] = ['id' => $this->customer->id, 'name' => $this->customer->contactInformation->name];
        $resource['warehouse_name'] = $this->contactInformation->name;
        $resource['warehouse_company_name'] = $this->contactInformation->company_name;
        $resource['warehouse_address'] = $this->contactInformation->address;
        $resource['warehouse_city'] = $this->contactInformation->city;
        $resource['warehouse_state'] = $this->contactInformation->state;
        $resource['warehouse_zip'] = $this->contactInformation->zip;
        $resource['warehouse_country'] = $this->contactInformation->country->iso_3166_2 ?? null;
        $resource['warehouse_email'] = $this->contactInformation->email;
        $resource['warehouse_phone'] = $this->contactInformation->phone;
        $resource['link_edit'] = route('warehouses.edit', ['warehouse' => $this]);
        $resource['link_delete'] = ['token' => csrf_token(), 'url' => route('warehouses.destroy', ['id' => $this->id, 'warehouse' => $this])];

        return $resource;
    }
}
