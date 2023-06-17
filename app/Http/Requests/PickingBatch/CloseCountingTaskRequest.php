<?php

namespace App\Http\Requests\CycleCountBatch;

use App\Http\Requests\FormRequest;

class CloseCountingTaskRequest extends FormRequest
{
    public static function validationRules()
    {
        $rules = [
            'customer_id' => [
                'required'
            ],
            'type' => [
                'required'
            ]
        ];

        return $rules;
    }
}
