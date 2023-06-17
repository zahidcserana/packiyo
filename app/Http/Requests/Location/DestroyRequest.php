<?php

namespace App\Http\Requests\Location;

use App\Http\Requests\FormRequest;

class DestroyRequest extends FormRequest
{
    public static function validationRules(): array
    {
        return [
            'id' => [
                'required', 'exists:locations,id,deleted_at,NULL'
            ]
        ];
    }
}
