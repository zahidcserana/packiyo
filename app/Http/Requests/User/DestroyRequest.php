<?php

namespace App\Http\Requests\User;

use App\Http\Requests\FormRequest;

class DestroyRequest extends FormRequest
{
    public static function validationRules()
    {
        $rules = [
            'email' => [
                'required',
                'email',
                'exists:users,email,deleted_at,NULL'
            ],
            'customer_id' => [
                'sometimes',
                'exists:customers,id,deleted_at,NULL'
            ]
        ];

        return $rules;
    }
}
