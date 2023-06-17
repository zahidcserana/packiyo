<?php

namespace App\Http\Requests\CycleCountBatch;

use App\Http\Requests\FormRequest;

class CycleCountBatchRequest extends FormRequest
{
    public static function validationRules()
    {
        $rules = [
            'customer_id' => [
                'required',
            ],
            'type' => [
                'required',
            ],
            'product_id' => [
                'sometimes',
            ],
            'location_id' => [
                'sometimes',
            ],
        ];

        return $rules;
    }
}
