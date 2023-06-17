<?php

namespace App\Http\Requests\OrderStatus;

use App\Http\Requests\FormRequest;

class UpdateRequest extends FormRequest
{
    public static function validationRules()
    {
        $rules = [
            'id' => [
                'required',
                'exists:order_statuses,id,deleted_at,NULL'
            ],
            'name' => [
                'required',
                'min:3'
            ],
            'color' => [
                'nullable',
                'string'
            ]
        ];

        return $rules;
    }
}
