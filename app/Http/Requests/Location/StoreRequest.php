<?php

namespace App\Http\Requests\Location;

use App\Http\Requests\FormRequest;
use App\Http\Requests\LocationProduct\StoreRequest as LocationProductStoreRequest;

class StoreRequest extends FormRequest
{
    public static function validationRules(): array
    {
        $rules = [
            'warehouse_id' => [
                'required',
                'exists:warehouses,id,deleted_at,NULL'
            ],
            'name' => [
                'required'
            ],
            'pickable' => [
                'sometimes'
            ],
            'disabled_on_picking_app' => [
                'sometimes'
            ],
            'sellable' => [
                'sometimes'
            ],
            'location_type_id' => [
                'nullable'
            ],
            'priority_counting_requested_at' => [
                'sometimes'
            ],
            'barcode' => [
                'sometimes'
            ],
        ];

        return array_merge_recursive($rules, LocationProductStoreRequest::prefixedValidationRules('location_product.*.'));
    }

    public function messages(): array
    {
        return [
            'name.required' => 'The name field is required',
            'location_product.0.product_id.*' => 'No product was selected'
        ];
    }
}
