<?php

namespace App\Http\Requests\PurchaseOrder;

use App\Http\Requests\FormRequest;
use App\Rules\Is3PLCustomer;

class ReceiveRequest extends FormRequest
{
    public static function validationRules(): array
    {
        return [
            'purchase_order_item_id' => [
                'required',
                'validate_purchase_order_item'
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
            ],

            'lot_tracking' => [
                'required',
                'integer'
            ],

            'lot_name' => [
                'exclude_if:lot_tracking,0',
                'nullable',
                'required_without:lot_id',
                'string'
            ],
            'expiration_date' => [
                'exclude_if:lot_tracking,0',
                'nullable',
                'required_without:lot_id',
                'string'
            ],
            'supplier_id' => [
                'exclude_if:lot_tracking,0',
                'nullable',
                'required_without:lot_id',
                'integer'
            ],
            'lot_id' => [
                'exclude_if:lot_tracking,0',
                'sometimes',
                'required_without:lot_name',
                'integer'
            ],
        ];
    }
}
