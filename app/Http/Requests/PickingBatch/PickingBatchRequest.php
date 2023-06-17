<?php

namespace App\Http\Requests\PickingBatch;

use App\Http\Requests\FormRequest;

class PickingBatchRequest extends FormRequest
{
    public static function validationRules()
    {
        $rules = [
            'quantity' => [
                'required',
                'integer'
            ],
            'customer_id' => [
                'required',
                'exists:customers,id,deleted_at,NULL',
                'check_orders',
                'check_task_type'
            ]
        ];

        return $rules;
    }
}
