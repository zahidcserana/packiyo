<?php

namespace App\Http\Requests\PurchaseOrder;

use App\Http\Requests\FormRequest;

class ReceiveBatchRequest extends FormRequest
{
    public static function validationRules(): array
    {
        return ReceiveRequest::prefixedValidationRules('*.', true);
    }
}
