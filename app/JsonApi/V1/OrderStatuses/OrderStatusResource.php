<?php

namespace App\JsonApi\V1\OrderStatuses;

use Illuminate\Http\Request;
use LaravelJsonApi\Core\Resources\JsonApiResource;

class OrderStatusResource extends JsonApiResource
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
            'customer_id' => $this->customer_id,
            'name' => $this->name,
            'fulfilled' => $this->fulfilled,
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
            'deleted_at' => $this->deleted_at,
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
            $this->relation('customer')->showDataIfLoaded()->withoutLinks(),
        ];
    }

}
