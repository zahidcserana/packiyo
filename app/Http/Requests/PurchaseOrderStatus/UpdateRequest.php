<?php

namespace App\Http\Requests\PurchaseOrderStatus;

use App\Http\Requests\FormRequest;

class UpdateRequest extends FormRequest
{
    public static function validationRules()
    {
        $rules = [
            'id' => [
                'required',
                'exists:purchase_order_statuses,id,deleted_at,NULL'
            ],
            'name' => [
                'required',
                'min:3'
            ]
        ];

        return $rules;
    }
}
