<?php

namespace App\JsonApi\V1\Locations;

use Illuminate\Http\Request;
use LaravelJsonApi\Core\Resources\JsonApiResource;

class LocationResource extends JsonApiResource
{

    /**
     * Get the resource's attributes.
     *
     * @param Request|null $request
     * @return iterable
     */
    public function attributes($request): iterable
    {
        $this->warehouse = $this->warehouse->load(['contactInformation','customer' => function ($query){
            return $query->with('contactInformation');
        }]);

        return [
            'id' => $this->id,
            'name' => $this->name,
            'barcode' => $this->barcode,
            'pickable' => $this->pickable,
            'customer' => $this->customer,
            'createdAt' => $this->created_at,
            'updatedAt' => $this->updated_at,
            'deletedAt' => $this->deleted_at,
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
            $this->relation('warehouse')->withoutLinks()->showDataIfLoaded()
        ];
    }
}
