<?php

namespace App\Http\Requests\Tote;

use App\Http\Requests\FormRequest;

class DestroyBatchRequest extends FormRequest
{
    public static function validationRules(): array
    {
        return DestroyRequest::prefixedValidationRules('*.', true);
    }
}
