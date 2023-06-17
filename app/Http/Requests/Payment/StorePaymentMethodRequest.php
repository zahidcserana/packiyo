<?php

namespace App\Http\Requests\Payment;

use App\Http\Requests\FormRequest;

class StorePaymentMethodRequest extends FormRequest
{
    public static function validationRules(): array
    {
        return [
            'payment_method' => [
                'required',
                'starts_with:pm_'
            ]
        ];
    }
}
