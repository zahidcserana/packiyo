<?php

namespace App\Http\Requests\Tote;

use App\Http\Requests\FormRequest;

class StoreBatchRequest extends FormRequest
{
    public static function validationRules(): array
    {
        return StoreRequest::prefixedValidationRules('*.', true);
    }
}
