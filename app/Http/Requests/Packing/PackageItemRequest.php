<?php

namespace App\Http\Requests\Packing;

use App\Http\Requests\FormRequest;

class PackageItemRequest extends FormRequest
{
    public static function validationRules()
    {
        $rules = [
            'items'=>[
                'required',
                'array',
                'min:1'
            ],
            'box' => [
                'required',
                'exists:shipping_boxes,id'
            ],
            'weight' => [
                'required',
                'numeric',
            ],
            '_length' => [
                'required',
                'numeric',
            ],
            'width' => [
                'required',
                'numeric',
            ],
            'height' => [
                'required',
                'numeric',
            ]
        ];

        return $rules;
    }
}
