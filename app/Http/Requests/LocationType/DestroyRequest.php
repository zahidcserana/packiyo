<?php

namespace App\Http\Requests\LocationType;

use App\Http\Requests\FormRequest;

class DestroyRequest extends FormRequest
{
    public static function validationRules(): array
    {
        return [
            'id' => [
                'required',
                'exists:location_types,id,deleted_at,NULL'
            ]
        ];
    }
}
