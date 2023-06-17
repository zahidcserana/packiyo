<?php

namespace App\Http\Requests\OrderStatus;

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
            'color' => [
                'nullable',
                'string'
            ]
        ];
    }
}
