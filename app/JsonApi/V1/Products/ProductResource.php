<?php

namespace App\JsonApi\V1\Products;

use Illuminate\Http\Request;
use LaravelJsonApi\Core\Resources\JsonApiResource;
use Webpatser\Countries\Countries;

class ProductResource extends JsonApiResource
{

    /**
     * Get the resource's attributes.
     *
     * @param Request|null $request
     * @return iterable
     */
    public function attributes($request): iterable
    {
        $this->customer = $this->customer->load(['contactInformation', 'parent.contactInformation']);

        return [
            'id' => $this->id,
            'sku' => $this->sku,
            'name' => $this->name,
            'price' => $this->price,
            'weight' => $this->weight,
            'height' => $this->height,
            'length' => $this->length,
            'width' => $this->width,
            'customs_price' => $this->customs_price,
            'customs_description' => $this->customs_description,
            'country_of_origin' => $this->country_of_origin,
            'hs_code' => $this->hs_code,
            'notes' => $this->notes,
            'quantity_on_hand' => $this->quantity_on_hand,
            'quantity_allocated' => $this->quantity_allocated,
            'quantity_available' => $this->quantity_available,
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
            'deleted_at' => $this->deleted_at,
            'quantity_backordered' => $this->quantity_backordered,
            'customer' => $this->customer,
            'locations' => $this->locations,
            'barcode' => $this->barcode,
            'images' => $this->productImages->take(-3)->values(),
            'priority_counting_requested_at' => $this->priority_counting_requested_at,
            'priority_counting_requested_at_change' => now()
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
            $this->relation('customer_information')->showDataIfLoaded()->withoutLinks()
        ];
    }

}
