<?php

namespace App\JsonApi\V1\Customers;

use Illuminate\Http\Request;
use LaravelJsonApi\Core\Resources\JsonApiResource;

class CustomerResource extends JsonApiResource
{

    /**
     * Get the resource's attributes.
     *
     * @param Request|null $request
     * @return iterable
     */
    public function attributes($request): iterable
    {
        return [
            'id' => $this->id,
            'createdAt' => $this->created_at,
            'updatedAt' => $this->updated_at,
            'deletedAt' => $this->deleted_at,
            'parent' => $this->parent?->load('contactInformation'),
            'contact_information' => $this->contactInformation,
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
            $this->relation('contact_information')->alwaysShowData()->withoutLinks(),
            $this->relation('parent')->alwaysShowData()->withoutLinks(),
        ];
    }

}
