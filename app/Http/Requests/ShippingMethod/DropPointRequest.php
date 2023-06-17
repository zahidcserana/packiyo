<?php

namespace App\Http\Requests\ShippingMethod;

use App\Http\Requests\FormRequest;

class DropPointRequest extends FormRequest
{
    public static function validationRules(): array
    {
        return [
            'order_id' => [
                'required',
                'exists:orders,id,deleted_at,NULL'
            ],
            'shipping_method_id' => [
                'integer',
                'required'
            ],
            'address' => [
                'string',
                'required'
            ],
            'city' => [
                'string',
                'required'
            ],
            'zip' => [
                'string',
                'required'
            ],
            'country_code' => [
                'string',
                'required'
            ],
            'preselect' => [
                'nullable'
            ],
            'q' => [
                'nullable'
            ]
        ];
    }
}
