<?php

namespace App\JsonApi\V1\Totes;

use Illuminate\Http\Request;
use LaravelJsonApi\Core\Resources\JsonApiResource;

class ToteResource extends JsonApiResource
{

    /**
     * Get the resource's attributes.
     *
     * @param Request|null $request
     * @return iterable
     */
    public function attributes($request): iterable
    {
        $this->orderIds = [];

        foreach ($this->placedToteOrderItems as $toteOrderItem) {
            $this->orderIds[] = $toteOrderItem->orderItem->order_id;
        }

        return [
            'id' => $this->id,
            'name' => $this->name,
            'barcode' => $this->barcode,
            'order' => $this->order,
            'picking_cart' => $this->pickingCart,
            'order_ids' => $this->orderIds,
            'warehouse' => $this->warehouse
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
            $this->relation('warehouse')->showDataIfLoaded()->withoutLinks(),
            $this->relation('pickingCart')->showDataIfLoaded()->withoutLinks(),
            $this->relation('order')->showDataIfLoaded()->withoutLinks()
        ];
    }
}
