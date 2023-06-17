<?php

namespace App\JsonApi\V1\Returns;

use Illuminate\Http\Request;
use LaravelJsonApi\Core\Resources\JsonApiResource;

class ReturnResource extends JsonApiResource
{

    /**
     * Get the resource's attributes.
     *
     * @param Request|null $request
     * @return iterable
     */
    public function attributes($request): iterable
    {
        $this->order = $this->order
            ->load(['customer', 'orderItems' => function ($query){
                return $query->with(['product' => function ($q){
                    return $q->with(['customer' => function ($subQuery){
                        return $subQuery->with('contactInformation');
                    }]);
                }]);
            },
            'shipments' => function ($shpQuery) {
                return $shpQuery->with('contactInformation');
            },
            'shippingContactInformation',
            'billingContactInformation',
            'orderLock',
        ]);

        $this->returnItems = $this->returnItems->load(['product' => function ($q){
            return $q->with(['customer' => function ($subQuery){
                return $subQuery->with('contactInformation');
            }]);
        }]);

        return [
            'id' => $this->id,
            'number' => $this->number,
            'requested_at' => user_date_time($this->requested_at),
            'expected_at' => user_date_time($this->expected_at),
            'received_at' => user_date_time($this->received_at),
            'reason' => $this->reason,
            'approved' => $this->approved,
            'notes' => $this->notes,
            'created_at' => user_date_time($this->created_at),
            'updated_at' => user_date_time($this->updated_at),
            'deleted_at' => user_date_time($this->deleted_at),
            'order' => $this->order,
            'returnItems' => $this->returnItems,
            'createdAt' => user_date_time($this->created_at),
            'updatedAt' => user_date_time($this->updated_at),
            'deletedAt' => user_date_time($this->updated_at),
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
            $this->relation('order')->showDataIfLoaded()->withoutLinks(),
        ];
    }

}
