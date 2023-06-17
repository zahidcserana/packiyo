<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class CustomerResource extends JsonResource
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

        $resource['parent'] = new CustomerResource($this->parent);
        unset($resource['parent_id']);

        $resource['contact_information'] = new ContactInformationResource($this->contactInformation);

        return $resource;
    }
}
