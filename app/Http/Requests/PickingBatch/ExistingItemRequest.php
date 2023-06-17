<?php

namespace App\Http\Requests\PickingBatch;

use App\Http\Requests\FormRequest;

class ExistingItemRequest extends FormRequest
{
    public static function validationRules()
    {
        $rules = [
            'task_id' => [
                'sometimes'
            ],
            'order_id' => [
                'sometimes'
            ],
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
