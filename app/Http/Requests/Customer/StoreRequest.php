<?php

namespace App\Http\Requests\Customer;

use App\Http\Requests\ContactInformation\StoreRequest as ContactInformationStoreRequest;
use App\Http\Requests\FormRequest;
use App\Models\CustomerSetting;

class StoreRequest extends FormRequest
{
    public static function validationRules($includeContactInformationRules = true): array
    {
        $rules = [
            'id' => [
                'sometimes'
            ],
            'locale' => [
                'sometimes'
            ],
            'weight_unit' => [
                'sometimes'
            ],
            'dimension_unit' => [
                'sometimes'
            ],
            'order_slip_logo' => [
                'nullable'
            ],
            'currency' => [
                'nullable',
                'string',
            ],
            'auto_return_label' => [
                'sometimes'
            ],
            'parent_customer_id' => [
                (auth()->user() && auth()->user()->isAdmin()) || app()->runningInConsole() ? 'nullable' : 'required',
                'exists:customers,id,deleted_at,NULL'
            ],
            'allow_child_customers' => [
                'sometimes'
            ]
        ];

        foreach (CustomerSetting::CUSTOMER_SETTING_KEYS as $key) {
            $rules[$key] = ['sometimes'];
        }

        if ($includeContactInformationRules) {
            $rules = array_merge_recursive($rules, ContactInformationStoreRequest::prefixedValidationRules('contact_information.'));

            $rules['contact_information.name'][0] = 'required';
            $rules['contact_information.country_id'][0] = 'required';
        }

        return $rules;
    }

    public function messages(): array
    {
        return [
            'contact_information.name.required' => 'The name field is required!',
            'contact_information.country_id.required' => 'Please select a country!'
        ];
    }
}
