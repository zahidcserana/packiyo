<?php

namespace App\Http\Requests\Tote;

use App\Http\Requests\FormRequest;

class UpdateRequest extends FormRequest
{
    public static function validationRules(): array
    {
        return [
            'warehouse_id' => [
                'required',
                'exists:warehouses,id,deleted_at,NULL'
            ],
            'name' => [
                'required',
                'string'
            ],
            'picking_cart_id' => [
                'required',
                'validate_picking_cart_capacity'
            ]
        ];
    }

    public function messages(): array
    {
        return [
            'picking_cart_id.validate_picking_cart_capacity' => 'Picking Cart capacity is full!'
        ];
    }
}
