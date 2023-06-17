<?php

namespace App\Http\Requests\Warehouses;

use App\Http\Requests\ContactInformation\StoreRequest as ContactInformationStoreRequest;
use App\Http\Requests\FormRequest;

class StoreRequest extends FormRequest
{

    public static function validationRules($includeContactInformationRules = true): array
    {
        $rules = [
            'customer_id' => [
                'required',
                'exists:customers,id,deleted_at,NULL'
            ],
            'contact_information.email' => [
                'required',
                'email'
            ],
            'contact_information.phone' => [
                'required',
                'numeric'
            ]
        ];

        if ($includeContactInformationRules) {
            $rules = array_merge_recursive($rules, ContactInformationStoreRequest::prefixedValidationRules('contact_information.'));
        }

        return $rules;
    }

    public function messages(): array
    {
        return [
            'contact_information.name.required' => 'The name field is required',
            'contact_information.address.required' => 'The address field is required',
            'contact_information.zip.required' => 'The ZIP field is required',
            'contact_information.phone.required' => 'The Phone field is required',
            'contact_information.city.required' => 'The City field is required',
            'contact_information.email.required' => 'The Email field is required',
            'contact_information.email.email' => 'The Email input is not a valid email address',
            'contact_information.phone.numeric' => 'The Phone input must be numeric'
        ];
    }
}
