<?php

namespace App\Http\Requests\EasypostCredential;

use App\Http\Requests\FormRequest;

class StoreRequest extends FormRequest
{
    public static function validationRules(): array
    {
        return [
            'customer_id' => [
                'sometimes',
                'exists:customers,id'
            ],
            'api_key' => [
                'required'
            ],
            'customs_signer' => [
                'nullable'
            ],
            'contents_explanation' => [
                'nullable'
            ],
            'eel_pfc' => [
                'nullable'
            ],
            'contents_type' => [
                'nullable'
            ]
        ];
    }
}
