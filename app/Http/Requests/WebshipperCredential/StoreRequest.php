<?php

namespace App\Http\Requests\WebshipperCredential;

use App\Http\Requests\FormRequest;
use Illuminate\Support\Str;

class StoreRequest extends FormRequest
{
    public static function validationRules()
    {
        return [
            'customer_id' => [
                'sometimes',
                'exists:customers,id'
            ],
            'api_base_url' => [
                'required',
                'url'
            ],
            'api_key' => [
                'required'
            ],
            'order_channel_id' => [
                'required',
                'integer'
            ]
        ];
    }

    protected function prepareForValidation(): void
    {
        $apiBaseUrl = $this->api_base_url;

        if (!Str::startsWith($apiBaseUrl, 'http')) {
            $this->merge([
                'api_base_url' => 'https://' . $apiBaseUrl . '.api.webshipper.io/v2/'
            ]);
        }
    }
}
