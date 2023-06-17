<?php

namespace App\Http\Requests\OrderChannel;

use App\Http\Requests\FormRequest;

class UpdateRequest extends FormRequest
{
    public static function validationRules()
    {
        $rules = [
            'id' => [
                'required',
                'exists:order_channels,id,deleted_at,NULL'
            ],
            'name' => [
                'required',
                'min:3'
            ]
        ];

        return $rules;
    }
}
