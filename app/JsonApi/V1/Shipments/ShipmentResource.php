<?php

namespace App\JsonApi\V1\Shipments;

use Illuminate\Http\Request;
use LaravelJsonApi\Core\Resources\JsonApiResource;

class ShipmentResource extends JsonApiResource
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
            'order_id' => $this->order_id,
            'tracking_code' => $this->tracking_code,
            'tracking_link' => $this->tracking_link,
            'contact_information' => $this->contactInformation,
            'shipment_items' => $this->shipmentItems
                ->load(['product'=>function ($query){
                return $query->with(['customer' => function ($subQ) {
                    return $subQ->with(['contactInformation', 'parent' => function ($sub){
                        return $sub->with('contactInformation');
                    }]);
                }]);
            }]),
            'createdAt' => $this->created_at,
            'updatedAt' => $this->updated_at,
            'deletedAt' => $this->deleted_at,
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
