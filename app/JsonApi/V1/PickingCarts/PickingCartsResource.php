<?php

namespace App\JsonApi\V1\PurchaseOrders;

use Illuminate\Http\Request;
use LaravelJsonApi\Core\Resources\JsonApiResource;

class PickingCartsResource extends JsonApiResource
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
            'warehouse_id' => $this->warehouse_id,
            'name' => $this->name,
            'barcode' => $this->barcode,
            'number_of_totes' => $this->number_of_totes,
            'createdAt' => $this->created_at,
            'updatedAt' => $this->updated_at,
            'deletedAt' => $this->updated_at,
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
            $this->relation('totes')->showDataIfLoaded()->withoutLinks()
        ];
    }

}
