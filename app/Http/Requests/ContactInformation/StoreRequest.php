<?php

namespace App\Http\Requests\ContactInformation;

use App\Http\Requests\FormRequest;

class StoreRequest extends FormRequest
{
    public static function validationRules(): array
    {
        return [
            'name' => [
                'nullable'
            ],
            'company_name' => [
                'nullable'
            ],
            'company_number' => [
                'nullable'
            ],
            'address' => [
                'nullable',
            ],
            'address2' => [
                'nullable'
            ],
            'zip' => [
                'nullable',
            ],
            'city' => [
                'nullable',
            ],
            'state' => [
                'nullable'
            ],
            'country_code' => [
                'nullable',
            ],
            'country_id' => [
                'nullable',
            ],
            'email' => [
                'nullable'
            ],
            'phone' => [
                'nullable'
            ]
        ];
    }
}
