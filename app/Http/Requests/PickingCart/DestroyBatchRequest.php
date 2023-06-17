<?php

namespace App\Http\Requests\PickingCart;

use App\Http\Requests\FormRequest;

class DestroyBatchRequest extends FormRequest
{
    public static function validationRules(): array
    {
        return DestroyRequest::prefixedValidationRules('*.', true);
    }
}
