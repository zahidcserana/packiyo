<?php

namespace App\Http\Requests\Return_;

use App\Http\Requests\FormRequest;
use App\Models\Order;
use App\Models\ReturnStatus;
use App\Rules\BelongsToCustomer;
use App\Rules\ExistsOrStaticValue;
use App\Rules\ValidReturnItemsRule;

class StoreRequest extends FormRequest
{
    public static function validationRules(): array
    {
        $order = Order::find((int)static::getInputField('order_id'));
        $customerId = $order->customer_id;

        return [
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
            'width' => [
                'nullable',
                'numeric',
            ],
            'height' => [
                'nullable',
                'numeric',
            ],
            'length' => [
                'nullable',
                'numeric',
            ],
            'weight' => [
                'nullable',
                'numeric',
            ],
            'items' => [
                'required',
                'min:1',
                new ValidReturnItemsRule,
            ],
            'items.*.quantity' => [
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
            'tags' => [
                'sometimes'
            ]
        ];
    }
}
