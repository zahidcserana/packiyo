<?php

namespace App\JsonApi\V1\TaskTypes;

use Illuminate\Http\Request;
use LaravelJsonApi\Core\Resources\JsonApiResource;

class TaskTypeResource extends JsonApiResource
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
            'name' => $this->name,
            'type' => $this->type,
            'createdAt' => $this->created_at,
            'updatedAt' => $this->updated_at,
            'deletedAt' => $this->deleted_at,
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
            // @TODO
        ];
    }

}
