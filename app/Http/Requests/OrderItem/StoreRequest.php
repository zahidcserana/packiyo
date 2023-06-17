<?php

namespace App\Http\Requests\OrderItem;

use App\Http\Requests\FormRequest;

class StoreRequest extends FormRequest
{
    public static $customerId;

    public static function validationRules()
    {
        return [
            'external_id' => [
                'sometimes',
                'distinct'
            ],
            'product_id' => [
                'required_without:sku',
                'exists:products,id'
            ],
            'sku' => [
                'required_without:product_id',
                'exists:products,sku,deleted_at,NULL,customer_id,' . static::$customerId
            ],
            'quantity' => [
                'required',
                'numeric',
                'min:0',
            ],
            'quantity_pending' => [
                'nullable',
                'numeric'
            ],
            'quantity_shipped' => [
                'nullable',
                'numeric'
            ],
            'price' => [
                'nullable',
                'numeric'
            ]
        ];
    }
}
