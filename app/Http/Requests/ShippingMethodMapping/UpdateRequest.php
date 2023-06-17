<?php

namespace App\Http\Requests\ShippingMethodMapping;

use App\Http\Requests\FormRequest;
use Illuminate\Support\Arr;

class UpdateRequest extends FormRequest
{
    public static function validationRules(): array
    {
        $rules = StoreRequest::validationRules();

        return Arr::except($rules, 'customer_id');
    }
}
