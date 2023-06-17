<?php

namespace App\Http\Requests\Return_;

use App\Http\Requests\FormRequest;
use App\Rules\Is3PLCustomer;

class ReceiveRequest extends FormRequest
{
    public static function validationRules(): array
    {
        return [
            'return_item_id' => [
                'required',
                'validate_return_item'
            ],
            'location_id' => [
                'required',
                'exists:locations,id,deleted_at,NULL'
            ],
            'quantity_received' => [
                'required',
                'numeric'
            ],
            'customer_id' => [
                'sometimes',
                new Is3PLCustomer()
            ]
        ];
    }
}
