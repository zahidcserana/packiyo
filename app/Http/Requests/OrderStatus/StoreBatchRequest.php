<?php

namespace App\Http\Requests\OrderStatus;

use App\Http\Requests\FormRequest;

class StoreBatchRequest extends FormRequest
{
    public static function validationRules()
    {
        return StoreRequest::prefixedValidationRules('*.');
    }
}
