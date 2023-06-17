<?php

namespace App\Http\Requests\ShippingMethodMapping;

use App\Http\Requests\FormRequest;

class DestroyRequest extends FormRequest
{
    public static function validationRules()
    {
        $rules = [
            'id' => [
                'required',
                'exists:shipping_method_mappings,id,deleted_at,NULL'
            ]
        ];

        return $rules;
    }
}
