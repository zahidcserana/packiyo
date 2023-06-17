<?php

namespace App\Http\Requests\Supplier;

use App\Http\Requests\ContactInformation\StoreRequest as ContactInformationStoreRequest;
use App\Http\Requests\FormRequest;

class StoreRequest extends FormRequest
{
    public static function validationRules($includeContactInformationRules = true)
    {
        $rules = [
            'customer_id' => [
                'required',
                'exists:customers,id,deleted_at,NULL'
            ],
            'currency' => [
                'nullable'
            ],
            'internal_note' => [
                'nullable'
            ],
            'default_purchase_order_note' => [
                'nullable'
            ]
        ];

        if ($includeContactInformationRules) {
            $rules = array_merge_recursive($rules, ContactInformationStoreRequest::prefixedValidationRules('contact_information.'));
        }

        return $rules;
    }
}
