<?php

namespace App\Http\Requests\ShippingMethodMapping;

use App\Http\Requests\FormRequest;

class StoreRequest extends FormRequest
{
    public static function validationRules(): array
    {
        return [
            'customer_id' => [
                'required',
                'exists:customers,id,deleted_at,NULL'
            ],
            'shipping_method_name' => [
                'required'
            ],
            'shipping_method_id' => [
                'required',
                'exists:shipping_methods,id,deleted_at,NULL'
            ],
            'return_shipping_method_id' => [
                'nullable',
                'exists:shipping_methods,id,deleted_at,NULL'
            ]
        ];
    }
}
