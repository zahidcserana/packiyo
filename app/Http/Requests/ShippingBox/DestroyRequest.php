<?php

namespace App\Http\Requests\ShippingBox;

use App\Http\Requests\FormRequest;

class DestroyRequest extends FormRequest
{
    public static function validationRules()
    {
        $rules = [
            'id' => [
                'required',
                'exists:shipping_boxes,id,deleted_at,NULL'
            ]
        ];

        return $rules;
    }
}
