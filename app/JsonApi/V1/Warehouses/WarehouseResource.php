<?php

namespace App\JsonApi\V1\Warehouses;

use Illuminate\Http\Request;
use LaravelJsonApi\Core\Resources\JsonApiResource;

class WarehouseResource extends JsonApiResource
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
            'deletedAt' => $this->deleted_at,
            'customer' => $this->customer,
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
            $this->relation('contact_information')->showDataIfLoaded()->withoutLinks(),
            $this->relation('customer')->showDataIfLoaded()->withoutLinks()
        ];
    }

}
