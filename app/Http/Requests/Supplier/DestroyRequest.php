<?php

namespace App\Http\Requests\Supplier;

use App\Http\Requests\FormRequest;

class DestroyRequest extends FormRequest
{
    public static function validationRules()
    {
        $rules = [
            'id' => [
                'required', 
                'exists:suppliers,id,deleted_at,NULL'
            ]
        ];

        return $rules;
    }
}
