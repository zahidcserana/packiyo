<?php

namespace App\Http\Requests\WebshipperCredential;

use App\Http\Requests\FormRequest;

class DestroyRequest extends FormRequest
{
    public static function validationRules(): array
    {
        return [
            'id' => [
                'required',
                'exists:webshipper_credentials,id,deleted_at,NULL'
            ]
        ];
    }
}
