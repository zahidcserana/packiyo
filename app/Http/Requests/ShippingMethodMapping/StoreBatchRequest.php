<?php

namespace App\Http\Requests\ShippingMethodMapping;

use App\Http\Requests\FormRequest;

class StoreBatchRequest extends FormRequest
{
    public static function validationRules()
    {
        return StoreRequest::prefixedValidationRules('*.', true);
    }
}
