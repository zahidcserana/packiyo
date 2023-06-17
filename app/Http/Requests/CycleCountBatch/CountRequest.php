<?php

namespace App\Http\Requests\CycleCountBatch;

use App\Http\Requests\FormRequest;

class CountRequest extends FormRequest
{
    public static function validationRules()
    {
        $rules = [
            'cycle_count_batch_item_id' => [
                'required'
            ],
            'quantity' => [
                'required',
                'integer'
            ],
            'type' => [
                'required',
            ],
        ];

        return $rules;
    }
}
