<?php

namespace App\JsonApi\V1\OrderItems;

use Illuminate\Http\Request;
use LaravelJsonApi\Core\Resources\JsonApiResource;

class OrderItemResource extends JsonApiResource
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
            'product' => $this->product,
            'toteOrderItems' => $this->toteOrderItems,
            'createdAt' => $this->created_at,
            'updatedAt' => $this->updated_at,
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
            $this->relation('product')->showDataIfLoaded()->withoutLinks(),
        ];
    }

}
