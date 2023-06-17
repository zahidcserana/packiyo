<?php

namespace App\Http\Requests\Task;

use App\Http\Requests\FormRequest;

class StoreRequest extends FormRequest
{
    public static function validationRules()
    {
        return [
            'user_id' => [
                'required',
                'exists:users,id'
            ],
            'customer_id' => [
                'required',
                'exists:customers,id,deleted_at,NULL'
            ],
            'task_type_id' => [
                'required',
                'exists:task_types,id,deleted_at,NULL'
            ],
            'notes' => [
                'required'
            ],
        ];
    }
}
