<?php

namespace App\Http\Requests\Tote;

use App\Http\Requests\FormRequest;

class DestroyRequest extends FormRequest
{
    public static function validationRules(): array
    {
        return [
            'id' => [
                'required',
                'exists:totes,id,deleted_at,NULL'
            ]
        ];
    }
}
