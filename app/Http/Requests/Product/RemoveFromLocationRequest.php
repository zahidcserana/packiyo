<?php

namespace App\Http\Requests\Product;

use App\Http\Requests\FormRequest;

class RemoveFromLocationRequest extends FormRequest
{
    public static function validationRules(): array
    {
        return [
            'location_id' => [
                'required'
            ]
        ];
    }
}
