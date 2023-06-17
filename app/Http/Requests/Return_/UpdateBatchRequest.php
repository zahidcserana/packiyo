<?php

namespace App\Http\Requests\Return_;

use App\Http\Requests\FormRequest;

class UpdateBatchRequest extends FormRequest
{
    public static function validationRules()
    {
        return UpdateRequest::prefixedValidationRules('*.');
    }
}
