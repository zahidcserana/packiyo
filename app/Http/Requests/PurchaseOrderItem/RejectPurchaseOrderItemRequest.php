<?php

namespace App\Http\Requests\PurchaseOrderItem;

use App\Http\Requests\FormRequest;

class RejectPurchaseOrderItemRequest extends FormRequest
{
    public static function validationRules(): array
    {
        return [
            'quantity.*' => [
                'required',
                'numeric',
                'min:1'
            ],
            'reason.*' => [
                'required'
            ],
            'note.*' => [
                'sometimes'
            ]
        ];
    }

    public function messages(): array
    {
        return [
            'reason.*.required' => 'The reason field cannot be empty',
            'quantity.*.min' => 'The quantity must be a number bigger than 0'
        ];
    }
}
