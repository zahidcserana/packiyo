<?php

namespace App\Http\Requests\PickingBatch;

use App\Http\Requests\FormRequest;

class ClosePickingTaskRequest extends FormRequest
{
    public static function validationRules()
    {
        $rules = [
            'picking_batch_id' => [
                'required'
            ]
        ];

        return $rules;
    }
}
