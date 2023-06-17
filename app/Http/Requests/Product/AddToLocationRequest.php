<?php

namespace App\Http\Requests\Product;

use App\Http\Requests\FormRequest;

class AddToLocationRequest extends FormRequest
{
    public static function validationRules(): array
    {
        return [
            'location_id' => [
                'required',
                'numeric'
            ],
            'quantity' => [
                'required',
                'numeric',
                'gt:0'
            ]
        ];
    }
}
