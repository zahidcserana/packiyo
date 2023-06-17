<?php

namespace App\Http\Requests\Tote;

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
            'name_prefix' => [
                'required',
                'string'
            ],
            'barcode' => [
                'nullable'
            ],
            'number_of_totes' => [
                'required',
                'numeric',
                'gt:0'
            ]
        ];
    }
}
