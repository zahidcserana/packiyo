<?php

namespace App\Http\Requests\OrderStatus;

use App\Http\Requests\FormRequest;

class DestroyBatchRequest extends FormRequest
{
    public static function validationRules()
    {
        return DestroyRequest::prefixedValidationRules('*.');
    }
}
