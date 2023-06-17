<?php

namespace App\Http\Requests\CycleCountBatch;

use App\Http\Requests\FormRequest;
use LaravelJsonApi\Validation\Rule as JsonApiRule;

class AvailableProductsRequest extends FormRequest
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
            'page' => [
                'nullable',
                'array',
                JsonApiRule::page(),
            ],
        ];

        return $rules;
    }
}
