<?php

namespace App\Http\Requests\LocationType;

use App\Http\Requests\FormRequest;

class UpdateRequest extends FormRequest
{
    public static function validationRules(): array
    {
        return [
            'id' => [
                'required',
                'exists:location_types,id,deleted_at,NULL'
            ],
            'customer_id' => [
                'exists:customers,id,deleted_at,NULL'
            ],
            'name' => [
                'required'
            ],
            'pickable' => [
                'required'
            ],
            'disabled_on_picking_app' => [
                'required'
            ],
            'sellable' => [
                'required'
            ]
        ];
    }
}
