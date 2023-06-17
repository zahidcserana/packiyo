<?php

namespace App\Http\Requests\Shipment;

use App\Http\Requests\FormRequest;

class ShipItemRequest extends FormRequest
{
    public static function validationRules(): array
    {
        return [
            'order_item_id' => [
                'required',
                'validate_order_item'
            ],
            'location_id' => [
                'required',
                'exists:locations,id'
            ],
            'tote_id' => [
                'nullable',
                'exists:totes,id'
            ],
            'quantity' => [
                'required',
                'numeric',
            ],
            'serial_number' => [
                'sometimes',
            ],
            'kit_count' => [
                'sometimes',
            ],
            'quantity_in_kit' => [
                'sometimes',
            ]
        ];
    }
}
