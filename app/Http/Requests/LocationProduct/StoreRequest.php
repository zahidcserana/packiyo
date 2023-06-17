<?php

namespace App\Http\Requests\LocationProduct;

use App\Http\Requests\FormRequest;

class StoreRequest extends FormRequest
{
    public static function validationRules(): array
    {
        return [
            'product_id' => [
                'sometimes',
                'exists:products,id'
            ],
            'quantity_on_hand' => [
                'required',
                'min:0'
            ],
            'location_product_id' => [
                'sometimes'
            ]
        ];
    }
}
