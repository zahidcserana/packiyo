<?php

namespace App\Http\Requests\ShippingBox;

use App\Http\Requests\FormRequest;

class StoreRequest extends FormRequest
{
    public static function validationRules()
    {
        return [
            'name' => [
                'required',
                'min:3'
            ],
            'customer_id' => [
                'required',
                'exists:customers,id'
            ],
            'width' => [
                'required',
                'numeric'
            ],
            'height' => [
                'required',
                'numeric'
            ],
            'length' => [
                'required',
                'numeric'
            ],
            'height_locked' => [
                'sometimes',
                'integer'
            ],
            'length_locked' => [
                'sometimes',
                'integer'
            ],
            'width_locked' => [
                'sometimes',
                'integer'
            ]
        ];
    }
}
