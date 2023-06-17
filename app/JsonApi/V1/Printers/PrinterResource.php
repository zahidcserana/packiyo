<?php

namespace App\JsonApi\V1\Printers;

use Illuminate\Http\Request;
use LaravelJsonApi\Core\Resources\JsonApiResource;

class PrinterResource extends JsonApiResource
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
            'customer_id' => $this->customer_id,
            'hostname' => $this->hostname,
            'name' => $this->name,
            'user_id' => $this->user_id,
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
            'deleted_at' => $this->deleted_at,
            'customer' => $this->customer,
            'user' => $this->user,
            'jobs' => $this->printJobs()->whereNull('status')->orWhere('status', '!=', 'PRINTED')->get(),
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
        ];
    }

}
