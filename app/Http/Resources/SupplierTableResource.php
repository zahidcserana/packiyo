<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class SupplierTableResource extends JsonResource
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

        $resource['supplier_name'] =  $this->contactInformation->name;
        $resource['supplier_address'] =  $this->contactInformation->address;
        $resource['supplier_zip'] = $this->contactInformation->zip;
        $resource['supplier_city'] = $this->contactInformation->city;
        $resource['supplier_email'] = $this->contactInformation->email;
        $resource['supplier_phone'] = $this->contactInformation->phone;
        $resource['customer'] = ['name' => $this->customer->contactInformation->name, 'url' => route('customer.edit', ['customer' => $this->customer])];
        $resource['link_edit'] = $this->id;
        $resource['link_delete'] = ['token' => csrf_token(), 'url' => route('supplier.destroy', ['id' => $this->id, 'supplier' => $this])];

        return $resource;
    }
}
