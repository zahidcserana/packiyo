<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class UserResource extends JsonResource
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

        $resource['contact_information'] = new ContactInformationResource($this->contactInformation);

        $resource['user_role'] = new UserRoleResource($this->role);
        unset($resource['user_role_id']);

        foreach ($this->customers as $key => $customer) {
            $resource['customer_user_role'][] = new CustomerUserResource($customer->pivot);
        }

        return $resource;
    }
}
