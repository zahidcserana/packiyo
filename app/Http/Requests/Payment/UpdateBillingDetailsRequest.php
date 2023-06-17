<?php

namespace App\Http\Requests\Payment;

use App\Http\Requests\FormRequest;

class UpdateBillingDetailsRequest extends FormRequest
{
    public static function validationRules(): array
    {
        return [
            'account_holder_name' => [
                'required'
            ],
            'address' => [
                'nullable'
            ],
            'address2' => [
                'nullable'
            ],
            'email' => [
                'nullable'
            ],
            'phone' => [
                'nullable',
            ],
            'postal_code' => [
                'required'
            ],
            'city' => [
                'nullable',
            ],
            'country_id' => [
                'nullable',
            ],
            'state' => [
                'nullable',
            ]
        ];
    }
}
