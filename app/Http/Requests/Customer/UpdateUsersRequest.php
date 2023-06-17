<?php

namespace App\Http\Requests\Customer;

use App\Http\Requests\FormRequest;

class UpdateUsersRequest extends FormRequest
{
    public static function validationRules($includeContactInformationRules = true)
    {
        $rules = [
            'new_user_id' => [
                'sometimes',
                'exists:users,id'
            ],
            'new_user_role_id' => [
                'sometimes',
                'exists:customer_user_roles,id'
            ],
            '*.user_id' => [
                'sometimes',
                'exists:users,id'
            ],
            '*.role_id' => [
                'sometimes',
                'exists:customer_user_roles,id'
            ]
        ];

        return $rules;
    }
}
