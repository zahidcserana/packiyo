<?php

namespace App\Http\Requests\ShippingBox;

use App\Http\Requests\FormRequest;

class UpdateRequest extends FormRequest
{
    public static function validationRules()
    {
        $rules = [
            'id' => [
                'required',
                'exists:shipping_boxes,id,deleted_at,NULL'
            ],
            'name' => [
                'required',
                'min:3'
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

        return $rules;
    }
}
