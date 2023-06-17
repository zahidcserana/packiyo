<?php

namespace App\Http\Requests\LocationProduct;

use App\Http\Requests\FormRequest;

class ImportInventoryRequest extends FormRequest
{
    public static function validationRules(): array
    {
        return [
            'customer_id' => [
                'required',
                'exists:customers,id,deleted_at,NULL'
            ],
            'warehouse_id' => [
                'required',
                'exists:warehouses,id,deleted_at,NULL'
            ],
            'inventory_csv' => [
                'required'
            ]
        ];
    }
}
