<?php

namespace App\Http\Requests\LocationType;

use App\Http\Requests\FormRequest;

class StoreRequest extends FormRequest
{
    public static function validationRules(): array
    {
        return [
            'customer_id' => [
                'required',
                'exists:customers,id,deleted_at,NULL'
            ],
            'name' => [
                'required'
            ],
            'pickable' => [
                'nullable'
            ],
            'disabled_on_picking_app' => [
                'nullable'
            ],
            'sellable' => [
                'nullable'
            ]
        ];
    }

    public function messages(): array
    {
        return [
            'customer_id.required' => 'The customer field is required',
            'name.required' => 'The nanme field is required'
        ];
    }
}
