<?php

namespace App\Http\Requests\EasypostCredential;

use App\Http\Requests\FormRequest;

class DestroyRequest extends FormRequest
{
    public static function validationRules(): array
    {
        return [
            'id' => [
                'required',
                'exists:easypost_credentials,id,deleted_at,NULL'
            ]
        ];
    }
}
