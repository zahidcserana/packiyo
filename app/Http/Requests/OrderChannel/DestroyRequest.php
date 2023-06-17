<?php

namespace App\Http\Requests\OrderChannel;

use App\Http\Requests\FormRequest;

class DestroyRequest extends FormRequest
{
    public static function validationRules()
    {
        $rules = [
            'id' => [
                'required',
                'exists:order_channels,id,deleted_at,NULL'
            ]
        ];

        return $rules;
    }
}
