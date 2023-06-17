<?php

namespace App\Http\Requests\WebshipperCredential;

class UpdateRequest extends StoreRequest
{
    public static function validationRules()
    {
        $rules = parent::validationRules();

        $rules['id'] = [
            'sometimes',
        	'exists:webshipper_credentials,id,deleted_at,NULL'
        ];

        return $rules;
    }
}
