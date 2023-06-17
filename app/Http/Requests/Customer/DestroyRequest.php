<?php

namespace App\Http\Requests\Customer;

use App\Http\Requests\FormRequest;

class DestroyRequest extends FormRequest
{
    public static function validationRules(): array
    {
        return [
            'id' => [
                'sometimes'
            ],
        ];
    }
}
