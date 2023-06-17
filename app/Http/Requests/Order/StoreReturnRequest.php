<?php

namespace App\Http\Requests\Order;

use App\Http\Requests\FormRequest;
use App\Models\ReturnStatus;
use App\Rules\BelongsToCustomer;
use App\Rules\ValidReturnItemsRule;
use App\Rules\ExistsOrStaticValue;

class StoreReturnRequest extends FormRequest
{
    public static function validationRules(): array
    {
        $customerId = static::getInputField('customer_id');

        return [
            'customer_id' => [
                'required',
                'exists:customers,id'
            ],
            'order_id' => [
                'required',
                'exists:orders,id,deleted_at,NULL',
            ],
            'number' => [
                'sometimes'
            ],
            'warehouse_id' => [
                'required',
                'exists:warehouses,id,deleted_at,NULL',
            ],
            'order_items' => [
                'required',
                'min:1',
                new ValidReturnItemsRule,
            ],
            'order_items.*.quantity' => [
                'required',
                'numeric',
            ],
            'reason' => [
                'nullable',
                'string',
            ],
            'return_status_id' => [
                'sometimes',
                new ExistsOrStaticValue('return_statuses', 'id', 'pending'),
                new BelongsToCustomer(ReturnStatus::class, $customerId, 'pending')
            ],
            'shipping_method_id' => [
                'required',
                new ExistsOrStaticValue('shipping_methods', 'id', 'dummy')
            ],
            'return_shipping_method_id' => [
                'nullable',
                new ExistsOrStaticValue('shipping_methods', 'id', 'dummy')
            ],
            'own_label' => [
                'nullable'
            ]
        ];
    }

    public function messages(): array
    {
        return [
            'order_items.required' => 'At least one order item needs to be selected',
        ];
    }
}
