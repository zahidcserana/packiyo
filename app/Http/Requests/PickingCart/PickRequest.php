<?php

namespace App\Http\Requests\PickingCart;

use App\Http\Requests\FormRequest;

class PickRequest extends FormRequest
{
    public static function validationRules(): array
    {
        return [
            'product_id' => [
                'required'
            ]
        ];
    }
}
