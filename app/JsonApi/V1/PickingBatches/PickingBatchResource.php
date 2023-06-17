<?php

namespace App\JsonApi\V1\PickingBatches;

use Illuminate\Http\Request;
use LaravelJsonApi\Core\Resources\JsonApiResource;

class PickingBatchResource extends JsonApiResource
{

    /**
     * Get the resource's attributes.
     *
     * @param Request|null $request
     * @return iterable
     */
    public function attributes($request): iterable
    {
        $pickingBatchItems = $this->pickingBatchItems->load([
            'location' => function ($query) {
                return [
                    $query->select([
                        'id',
                        'name'
                    ])
                ];
            },
            'orderItem' => function ($query) {
                return [
                    $query->with(['product' => function ($query) {
                        return $query->with([
                            'productImages',
                            'locations'
                        ])->select([
                            'id',
                            'sku',
                            'name',
                            'barcode',
                            'quantity_on_hand'
                        ]);
                    }])->with(['order' => function ($query) {
                        return $query->select([
                            'id',
                            'number'
                        ]);
                    }])->select([
                        'id',
                        'order_id',
                        'product_id'
                    ])
                ];
            },
        ])->map(function ($pickingBatchItem) {
            return collect($pickingBatchItem)->only([
                'id',
                'quantity',
                'quantity_picked',
                'picking_batch_id',
                'order_item_id',
                'order_item',
                'location_id',
                'location',
                'order_item'
            ]);
        });

        return [
            'id' => $this->id,
            'picking_batch_item' => $pickingBatchItems,
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
        return [];
    }

}
