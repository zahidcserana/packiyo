<?php

namespace App\JsonApi\V1\Suppliers;

use Illuminate\Http\Request;
use LaravelJsonApi\Core\Resources\JsonApiResource;

class SupplierResource extends JsonApiResource
{

    /**
     * Get the resource's attributes.
     *
     * @param Request|null $request
     * @return iterable
     */
    public function attributes($request): iterable
    {
        $this->customer = $this->customer->load('contactInformation');

        return [
            'id' => $this->id,
            'createdAt' => $this->created_at,
            'updatedAt' => $this->updated_at,
            'deletedAt' => $this->updated_at,
            'contact_information' => $this->contactInformation,
            'customer' => $this->customer,
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
            $this->relation('contact_information')->showDataIfLoaded()->withoutLinks()
        ];
    }

}
