<?php

namespace App\Http\Requests\Warehouses;

use App\Http\Requests\FormRequest;

class DestroyRequest extends FormRequest
{
    public static function validationRules()
    {
        $rules = [
            'id' => [
                'required',
            ],
            'customer_id' => [
                'sometimes',
                'exists:customers,id,deleted_at,NULL'
            ]
        ];

        return $rules;
    }
}
