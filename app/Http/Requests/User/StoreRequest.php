<?php

namespace App\Http\Requests\User;

use App\Http\Requests\ContactInformation\StoreRequest as ContactInformationStoreRequest;
use App\Http\Requests\FormRequest;

class StoreRequest extends FormRequest
{
    public static function validationRules($includeContactInformationRules = true)
    {
        $rules = [
            'email' => [
                'required',
                'email'
            ],
            'password' => [
                'confirmed',
                'min:6',
                'regex:/[a-zA-Z0-9@$!%*#?&]/',
            ],
            'password_confirmation' => [
                'required'
            ],
            'customer_id' => [
                'required',
                'exists:customers,id,deleted_at,NULL'
            ],
            'user_role_id' => [
                'exists:user_roles,id,deleted_at,NULL',
            ],
            'customer_user_role_id' => [
                'exists:user_roles,id,deleted_at,NULL',
            ],
        ];

        if ($includeContactInformationRules) {
            $rules = array_merge_recursive($rules, ContactInformationStoreRequest::prefixedValidationRules('contact_information.'));
        }

        return $rules;
    }
}
