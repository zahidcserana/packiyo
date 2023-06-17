<?php

namespace App\Http\Requests\Product;

use App\Http\Requests\FormRequest;

class TransferRequest extends FormRequest
{
    public static function validationRules(): array
    {
        return [
            'from_location_id' => [
                'required',
                'numeric'
            ],
            'to_location_id' => [
                'required',
                'numeric'
            ],
            'quantity' => [
                'required',
                'numeric',
                'gt:0'
            ]
        ];
    }
}
