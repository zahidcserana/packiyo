<?php

namespace App\Http\Requests\Product;

use App\Http\Requests\FormRequest;
use App\Http\Requests\Image\ImageRequest;
use App\Models\Product;
use App\Rules\UniqueForCustomer;

class StoreRequest extends FormRequest
{
    public static function validationRules(): array
    {
        $customerId = static::getInputField('customer_id');

        $rules = [
            'sku' => [
                'required',
                new UniqueForCustomer(Product::class, $customerId)
            ],
            'name' => [
                'required',
            ],
            'price' => [
                'nullable',
                'numeric'
            ],
            'notes' => [
                'sometimes'
            ],
            'customer_id' => [
                'required',
                'exists:customers,id'
            ],
            'width' => [
                'nullable',
                'numeric'
            ],
            'height' => [
                'nullable',
                'numeric'
            ],
            'length' => [
                'nullable',
                'numeric'
            ],
            'weight' => [
                'nullable',
                'numeric'
            ],
            'barcode' => [
                'nullable',
            ],
            'suppliers' => [
                'sometimes',
                'array',
            ],
            'suppliers.*' => [
                'required',
                'integer'
            ],
            'is_kit' => [
                'sometimes'
            ],
            'lot_tracking' => [
                'sometimes'
            ],
            'lot_priority' => [
                'integer',
                'sometimes'
            ],
            'kit_type' => [
                'sometimes',
            ],
            'kit_items' => [
                'required_if:is_kit,1'
            ],
            'kit_items.*' => [
                'required_if:is_kit,1',
            ],
            'kit_items.*.id' => [
                'required_if:is_kit,1',
                'integer'
            ],
            'kit_items.*.quantity' => [
                'required_if:is_kit,1'
            ],
            'kit-quantity.*' => [
                'sometimes',
            ],
            'value' => [
                'nullable',
                'numeric'
            ],
            'customs_price' => [
                'nullable',
                'numeric'
            ],
            'customs_description' => [
                'nullable',
                'string'
            ],
            'hs_code' => [
                'nullable',
                'string'
            ],
            'country_of_origin' => [
                'nullable',
                'exists:countries,id'
            ],
            'country_of_origin_code' => [
                'nullable',
            ],
            'update_vendor' => [
                'sometimes',
            ],
            'product_locations' => [
                'array'
            ],
            'product_lots' => [
                'array'
            ],
            'product_locations.*.id' => [
                'sometimes',
                'numeric',
            ],
            'product_lots.*.id' => [
                'sometimes',
                'required_unless:product_locations.*.quantity,gt,0'
            ],
            'product_locations.*.quantity' => [
                'sometimes',
                'numeric'
            ],
            'file.*' => [
                'sometimes'
            ],
            'priority_counting_requested_at' => [
                'sometimes'
            ],
            'has_serial_number' => [
                'sometimes'
            ],
            'tags' => [
                'sometimes'
            ],
            'reorder_threshold' => [
                'nullable',
                'numeric'
            ],
            'quantity_reorder' => [
                'nullable',
                'numeric'
            ],
        ];

        return array_merge_recursive($rules, ImageRequest::prefixedValidationRules('product_images.*.'));
    }

    public function attributes(): array
    {
        return [
            'sku' => 'SKU',
            'name' => 'NAME',
            'price' => 'PRICE',
            'notes' => 'NOTES',
            'width' => 'WIDTH',
            'height' => 'HEIGHT',
            'length' => 'LENGTH',
            'weight' => 'WEIGHT',
            'barcode' => 'BARCODE',
            'suppliers' => 'SUPPLIERS',
            'is_kit' => 'IS KIT',
            'kit_type' => 'KIT TYPE',
            'kit_items' => 'KIT ITEMS',
        ];
    }
}
