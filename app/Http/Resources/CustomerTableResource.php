<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class CustomerTableResource extends JsonResource
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

        $resource['name'] = $this->contactInformation->name;
        $resource['company_name'] = $this->contactInformation->company_name;
        $resource['address'] = $this->contactInformation->address;
        $resource['address2'] = $this->contactInformation->address2;
        $resource['zip'] = $this->contactInformation->zip;
        $resource['city'] = $this->contactInformation->city;
        $resource['email'] = $this->contactInformation->email;
        $resource['phone'] = $this->contactInformation->phone;
        $resource['link_edit'] =  route('customer.edit', ['customer' => $this]);
        $resource['link_delete'] = ['token' => csrf_token(), 'url' => route('customer.destroy', ['id' => $this->id, 'customer' => $this])];

        return $resource;
    }
}
