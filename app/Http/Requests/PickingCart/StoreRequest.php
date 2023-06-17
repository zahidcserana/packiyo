<?php

namespace App\Http\Requests\PickingCart;

use App\Http\Requests\FormRequest;

class StoreRequest extends FormRequest
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
            'number_of_totes' => [
                'required',
                'integer',
                'gt:0'
            ]
        ];
    }
}
