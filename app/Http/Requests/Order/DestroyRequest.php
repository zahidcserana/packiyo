<?php

namespace App\Http\Requests\Order;

use App\Http\Requests\FormRequest;

class DestroyRequest extends FormRequest
{
    public static function validationRules()
    {
        $rules = [
            'id' => [
                'required', 
                'exists:orders,id,deleted_at,NULL'
            ]
        ];

        return $rules;
    }
}
