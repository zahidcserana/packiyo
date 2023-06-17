<?php

namespace App\JsonApi\V1\PlacedToteOrderItems;

use Illuminate\Http\Request;
use LaravelJsonApi\Core\Resources\JsonApiResource;

class PlacedToteOrderItemResource extends JsonApiResource
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
            'quantity' => $this->quantity,
            'picked_at' => $this->picked_at,
            'removed_at' => $this->removed_at,
            'tote' => $this->tote,
            'order_item' => $this->orderItem
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
            $this->relation('orderItem')->showDataIfLoaded()->withoutLinks(),
            $this->relation('tote')->showDataIfLoaded()->withoutLinks()
        ];
    }

}
