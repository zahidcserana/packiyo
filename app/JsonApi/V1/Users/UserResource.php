<?php

namespace App\JsonApi\V1\Users;

use App\Http\Resources\CustomerUserResource;
use Illuminate\Http\Request;
use LaravelJsonApi\Core\Resources\JsonApiResource;

class UserResource extends JsonApiResource
{
    protected $customer_user_role;
    /**
     * Get the resource's attributes.
     *
     * @param Request|null $request
     * @return iterable
     */
    public function attributes($request): iterable
    {
        foreach ($this->customers as $customer) {
            $customer->pivot?->load('role');
            $this->customer_user_role[] = $customer->pivot;
        }

        return [
            'id' => $this->id,
            'email' => $this->email,
            'picture' => $this->picture,
            'createdAt' => $this->created_at,
            'updatedAt' => $this->updated_at,
            'deletedAt' => $this->deleted_at,
            'contact_information' => $this->contactInformation,
            'user_role' => $this->role,
            'customer_user_role' => $this->customer_user_role,
        ];
    }

    /**
     * Get the resource's relationships.
     *
     * @param Request|null $request
     * @return iterable
     */
    public function relationships($request): iterable
    {
        return [
            $this->relation('contact_information')->showDataIfLoaded()->withoutLinks(),
            $this->relation('user_role','role')->showDataIfLoaded()->withoutLinks(),
        ];
    }

}
