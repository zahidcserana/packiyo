<?php

namespace App\Http\Requests\Webhook;

use App\Http\Requests\FormRequest;

class DestroyRequest extends FormRequest
{
    public static function validationRules()
    {
        $rules = [
            'id' => [
                'required', 
                'exists:webhooks,id,deleted_at,NULL'
            ]
        ];

        return $rules;
    }
}
