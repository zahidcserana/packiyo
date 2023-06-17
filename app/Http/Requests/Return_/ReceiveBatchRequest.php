<?php

namespace App\Http\Requests\Return_;

use App\Http\Requests\FormRequest;

class ReceiveBatchRequest extends FormRequest
{
    public static function validationRules(): array
    {
        return ReceiveRequest::prefixedValidationRules('*.');
    }
}
