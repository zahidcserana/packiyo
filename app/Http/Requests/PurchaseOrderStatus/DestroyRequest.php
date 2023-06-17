<?php

namespace App\Http\Requests\PurchaseOrderStatus;

use App\Http\Requests\FormRequest;

class DestroyRequest extends FormRequest
{
    public static function validationRules()
    {
        $rules = [
            'id' => [
                'required',
                'exists:purchase_order_statuses,id,deleted_at,NULL'
            ]
        ];

        return $rules;
    }
}
