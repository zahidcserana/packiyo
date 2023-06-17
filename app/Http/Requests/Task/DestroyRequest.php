<?php

namespace App\Http\Requests\Task;

use App\Http\Requests\FormRequest;

class DestroyRequest extends FormRequest
{
    public static function validationRules()
    {
        $rules = [
            'id' => [
                'required', 
                'exists:tasks,id,deleted_at,NULL'
            ]
        ];

        return $rules;
    }
}
