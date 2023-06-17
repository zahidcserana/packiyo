<?php

namespace App\Http\Requests\Product;

use App\Http\Requests\FormRequest;

class SetLotRequest extends FormRequest
{
    public static function validationRules(): array
    {
        $rules = [

            'name' => [
                'required',
                'string'
            ],
            'expiration_date' => [
                'nullable',
                'string'
            ],
            'location_id' => [
                'required',
                'integer',
                'exists:locations,id'
            ],
            'product_id' => [
                'required',
                'integer',
                'exists:products,id'
            ],
            'quantity_added' => [
                'required',
                'integer',
                'min:1'
            ],
            'quantity_removed' => [
                'required',
                'integer'
            ]
        ];

        return $rules;
    }
}
