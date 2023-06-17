<?php

namespace App\Http\Requests\TaskType;

use App\Http\Requests\FormRequest;

class UpdateRequest extends FormRequest
{
    public static function validationRules()
    {
        $rules = StoreRequest::validationRules();

        $rules['id'] = ['required', 'exists:task_types,id,deleted_at,NULL'];

        return $rules;
    }
}
