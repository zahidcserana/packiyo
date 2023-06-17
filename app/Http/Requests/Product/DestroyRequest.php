<?php

namespace App\Http\Requests\Product;

use App\Http\Requests\FormRequest;

class DestroyRequest extends FormRequest
{
    public static function validationRules(): array
    {
        return [
            'id' => [
                'nullable'
            ]
        ];
    }
}
