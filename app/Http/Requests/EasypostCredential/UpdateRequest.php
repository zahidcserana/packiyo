<?php

namespace App\Http\Requests\EasypostCredential;

use App\Http\Requests\FormRequest;

class UpdateRequest extends FormRequest
{
    public static function validationRules()
    {
        $rules = StoreRequest::validationRules();

        $rules['id'] = [
            'sometimes',
        	'exists:easypost_credentials,id,deleted_at,NULL'
        ];

        return $rules;
    }
}
