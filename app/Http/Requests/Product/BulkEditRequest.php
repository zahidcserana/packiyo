<?php

namespace App\Http\Requests\Product;

use App\Http\Requests\FormRequest;

class BulkEditRequest extends FormRequest
{
    public static function validationRules(): array
    {
        return [
            'ids' => [
                'required'
            ],
            'add_tags' => [
                'nullable',
                'array'
            ],
            'remove_tags' => [
                'nullable',
                'array'
            ],
            'lot_tracking' => [
                'nullable',
                'integer'
            ],
            'priority_counting_requested_at' => [
                'nullable',
                'integer'
            ],
            'has_serial_number' => [
                'nullable',
                'integer'
            ],
            'remove_empty_locations' => [
                'nullable',
                'integer'
            ],
            'hs_code' => [
                'nullable',
                'string'
            ],
            'notes' => [
                'nullable',
                'string'
            ],
            'reorder_threshold' => [
                'nullable',
                'integer'
            ],
            'quantity_reorder' => [
                'nullable',
                'integer'
            ],
            'country_id' => [
                'nullable',
                'integer'
            ],
            'vendor_id' => [
                'nullable',
                'integer'
            ]
        ];
    }
}
