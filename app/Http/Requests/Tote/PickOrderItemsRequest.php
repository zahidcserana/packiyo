<?php

namespace App\Http\Requests\Tote;

use App\Http\Requests\FormRequest;

class PickOrderItemsRequest extends FormRequest
{
    public static function validationRules(): array
    {
        return [
            'order_item_id' => [
                'required',
                'exists:order_items,id,deleted_at,NULL'
            ],
            'quantity' => [
                'required',
                'integer'
            ]
        ];
    }
}
