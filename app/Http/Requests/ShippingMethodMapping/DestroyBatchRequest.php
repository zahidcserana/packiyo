<?php

namespace App\Http\Requests\ShippingMethodMapping;

use App\Http\Requests\FormRequest;

class DestroyBatchRequest extends FormRequest
{
    public static function validationRules()
    {
        return DestroyRequest::prefixedValidationRules('*.', true);
    }
}
