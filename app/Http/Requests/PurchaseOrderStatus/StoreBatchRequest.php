<?php

namespace App\Http\Requests\PurchaseOrderStatus;

use App\Http\Requests\FormRequest;

class StoreBatchRequest extends FormRequest
{
    public static function validationRules()
    {
        return StoreRequest::prefixedValidationRules('*.');
    }
}
