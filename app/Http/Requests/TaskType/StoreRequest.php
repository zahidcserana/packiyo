<?php

namespace App\Http\Requests\TaskType;

use App\Http\Requests\FormRequest;

class StoreRequest extends FormRequest
{
    public static function validationRules()
    {
        return [
            'name' => [
                'required'
            ],
            'customer_id' => [
                'required',
                'exists:customers,id'
            ]
        ];
    }
}
