<?php

namespace App\Http\Requests\ReturnItem;

use App\Http\Requests\FormRequest;

class StoreRequest extends FormRequest
{
    public static function validationRules()
    {
        return [
            'product_id' => [
                'required',
                'exists:products,id,deleted_at,NULL'
            ],
            'quantity' => [
                'required',
                'numeric',
                'min:0'
            ],
            'quantity_received' => [
                'sometimes',
                'numeric'
            ]
        ];
    }
}
