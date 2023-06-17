<?php

namespace App\Http\Requests\Order;

use App\Http\Requests\FormRequest;
use App\Rules\ExistsOrStaticValue;

class BulkEditRequest extends FormRequest
{
    public static function validationRules(): array
    {
        return [
            'ids' => [
                'required'
            ],
            'add_tags' => [
                'nullable',
                'array'
            ],
            'remove_tags' => [
                'nullable',
                'array'
            ],
            'packing_note' => [
                'nullable',
                'string'
            ],
            'slip_note' => [
                'nullable',
                'string'
            ],
            'gift_note' => [
                'nullable',
                'string'
            ],
            'internal_note' => [
                'nullable',
                'string'
            ],
            'priority' => [
                'nullable'
            ],
            'allow_partial' => [
                'nullable'
            ],
            'add_address_hold' => [
                'nullable'
            ],
            'add_fraud_hold' => [
                'nullable'
            ],
            'add_allocation_hold' => [
                'nullable'
            ],
            'add_payment_hold' => [
                'nullable'
            ],
            'add_operator_hold' => [
                'nullable'
            ],
            'remove_address_hold' => [
                'nullable'
            ],
            'remove_fraud_hold' => [
                'nullable'
            ],
            'remove_allocation_hold' => [
                'nullable'
            ],
            'remove_payment_hold' => [
                'nullable'
            ],
            'remove_operator_hold' => [
                'nullable'
            ],
            'remove_all_holds' => [
                'nullable'
            ],
            'shipping_method_id' => [
                'nullable',
                'integer'
            ],
            'country_id' => [
                'nullable',
                'integer'
            ]
        ];
    }
}
