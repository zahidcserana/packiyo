<?php

namespace App\Http\Requests\Order;

use App\Http\Requests\FormRequest;

class DestroyBatchRequest extends FormRequest
{
    public static function validationRules()
    {
        return DestroyRequest::prefixedValidationRules('*.', true);
    }
}
