<?php

namespace App\Http\Requests\Lot;

use App\Http\Requests\FormRequest;

class DestroyRequest extends FormRequest
{
    public static function validationRules()
    {
        $rules = [
            'id' => [
                'required',
                'exists:lots,id,deleted_at,NULL'
            ]
        ];

        return $rules;
    }
}
