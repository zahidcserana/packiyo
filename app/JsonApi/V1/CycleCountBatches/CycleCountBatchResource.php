<?php

namespace App\JsonApi\V1\CycleCountBatches;

use Illuminate\Http\Request;
use LaravelJsonApi\Core\Resources\JsonApiResource;

class CycleCountBatchResource extends JsonApiResource
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
            'createdAt' => $this->created_at,
            'updatedAt' => $this->updated_at,
            'cycle_count_batch_item' => $this->cycleCountBatchItems->load([
                'location',
                'product' => function($query) {
                    return $query->with([
                        'productImages',
                    ]);
                }
            ]),
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
