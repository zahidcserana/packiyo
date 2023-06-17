<?php

namespace App\Http\Requests\CycleCountBatch;

use App\Http\Requests\FormRequest;

class PickRequest extends FormRequest
{
    public static function validationRules()
    {
        $rules = [
            'cycle_count_batch_item_id' => [
                'required'
            ]
        ];

        return $rules;
    }
}
