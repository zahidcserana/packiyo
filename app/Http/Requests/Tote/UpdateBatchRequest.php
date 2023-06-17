<?php

namespace App\Http\Requests\Tote;

use App\Http\Requests\FormRequest;

class UpdateBatchRequest extends FormRequest
{
    public static function validationRules(): array
    {
        return UpdateRequest::prefixedValidationRules('*.', true);
    }
}
