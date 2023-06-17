<?php

namespace App\Http\Requests\Product;

use App\Http\Requests\FormRequest;

class ChangeLocationQuantityRequest extends FormRequest
{
    public static function validationRules(): array
    {
        return [
            'location_id' => [
                'required'
            ],
            'quantity' => [
                'nullable',
                'numeric'
            ],
            'quantity_available' => [
                'nullable',
                'numeric'
            ]
        ];
    }
}
