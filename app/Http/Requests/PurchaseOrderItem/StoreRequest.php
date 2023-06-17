<?php

namespace App\Http\Requests\PurchaseOrderItem;

use App\Http\Requests\FormRequest;

class StoreRequest extends FormRequest
{
    public static function validationRules()
    {
        return [
            'external_id' => [
                'sometimes',
                'distinct'
            ],
            'product_id' => [
                'required',
                'exists:products,id,deleted_at,NULL'
            ],
            'quantity' => [
                'required',
                'numeric',
                'min:0'
            ],
            'purchase_order_id' => [
                'sometimes'
            ],
            'quantity_received' => [
                'sometimes'
            ],
            'quantity_sell_ahead' => [
                'sometimes',
                'numeric',
                'min:0'
            ]
        ];
    }
}
