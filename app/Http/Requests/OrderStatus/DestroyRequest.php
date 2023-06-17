<?php

namespace App\Http\Requests\OrderStatus;

use App\Http\Requests\FormRequest;

class DestroyRequest extends FormRequest
{
    public static function validationRules()
    {
        $rules = [
            'id' => [
                'required',
                'exists:order_statuses,id,deleted_at,NULL'
            ]
        ];

        return $rules;
    }
}
