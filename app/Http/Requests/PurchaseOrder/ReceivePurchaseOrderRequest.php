<?php

namespace App\Http\Requests\PurchaseOrder;

use App\Http\Requests\FormRequest;

class ReceivePurchaseOrderRequest extends FormRequest
{
    public static function validationRules(): array
    {
        return [
            'location_id' => [
                'array',
                'required',
                'min:1'
            ],

            'lot_id' => [
                'array',
                'sometimes'
            ],
            'lot_name' => [
                'array',
                'sometimes'
            ],
            'expiration_date' => [
                'array',
                'sometimes'
            ],
            'supplier_id' => [
                'array',
                'sometimes'
            ],
            'lot_tracking' => [
                'array',
                'required'
            ],

            'lot_tracking.*' => [
                'required',
                'integer'
            ],

            'location_id.*' => [
                'exists:locations,id,deleted_at,NULL'
            ],
            'quantity_received.*' => [
                'required',
                'numeric'
            ],
            'product_id.*' =>[
                'required',
                'numeric',
                'exists:products,id,deleted_at,NULL'
            ],

            'lot_name.*' => [
                'exclude_if:lot_tracking.*,0',
                'nullable',
                'string',
                'required_without:lot_id.*'
            ],
            'expiration_date.*' => [
                'exclude_if:lot_tracking.*,0',
                'nullable',
                'string',
                'required_without:lot_id.*'
            ],
            'supplier_id.*'=> [
                'exclude_if:lot_tracking.*,0',
                'nullable',
                'integer',
                'required_without:lot_id.*'
            ],
            'lot_id.*' => [
                'exclude_if:lot_tracking.*,0',
                'nullable',
                'integer',
            ]
        ];
    }
}
