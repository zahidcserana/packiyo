<?php

namespace App\JsonApi\V1\ReturnItems;

use Illuminate\Http\Request;
use LaravelJsonApi\Core\Resources\JsonApiResource;

class ReturnItemResource extends JsonApiResource
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
            'return_id' => $this->purchase_order_id,
            'quantity' => $this->quantity,
            'quantity_received' => $this->quantity_received,
            'createdAt' => user_date_time($this->created_at),
            'updatedAt' => user_date_time($this->updated_at),
            'deletedAt' => user_date_time($this->deleted_at),
            'product' => $this->product->load([
                'customer'=> function($sub) {
                    return $sub->with(['contactInformation', 'parent'=> function($query) {
                        return $query->with('contactInformation');
                    }]);
                },
                'location' => function ($locQ){
                    return $locQ->with(['warehouse' => function($locSQ) {
                        return $locSQ->with(['contactInformation', 'customer'=> function($subLocS) {
                            return $subLocS->with(['contactInformation', 'parent'=> function($subLocSQ) {
                                return $subLocSQ->with('contactInformation');
                            }]);
                        }]);
                    }]);
                }]),
            'location' => $this->product->load([])->location
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
