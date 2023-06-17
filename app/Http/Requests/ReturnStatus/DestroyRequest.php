<?php

namespace App\Http\Requests\ReturnStatus;

use App\Http\Requests\FormRequest;

class DestroyRequest extends FormRequest
{
    public static function validationRules()
    {
        $rules = [
            'id' => [
                'required',
                'exists:return_statuses,id,deleted_at,NULL'
            ]
        ];

        return $rules;
    }
}
