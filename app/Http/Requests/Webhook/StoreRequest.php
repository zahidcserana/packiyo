<?php

namespace App\Http\Requests\Webhook;

use App\Http\Requests\FormRequest;
use App\Models\OrderChannel;
use App\Models\Webhook;
use App\Rules\BelongsToCustomer;
use App\Rules\WebhookObjectTypeRule;
use Illuminate\Validation\Rule;

class StoreRequest extends FormRequest
{
    public static function validationRules()
    {
        $customerId = static::getInputField('customer_id');

        return [
            'customer_id' => [
                'required',
                'exists:customers,id,deleted_at,NULL'
            ],
            'order_channel_id' => [
                'sometimes',
                new BelongsToCustomer(OrderChannel::class, $customerId)
            ],
            'name' => [
                'required'
            ],
            'object_type' => [
                'required',
                new WebhookObjectTypeRule()
            ],
            'operation' => [
                'required',
                Rule::in([
                    Webhook::OPERATION_TYPE_STORE,
                    Webhook::OPERATION_TYPE_UPDATE,
                    Webhook::OPERATION_TYPE_DESTROY
                ]),
            ],
            'url' => [
                'required',
                'url'
            ],
            'secret_key' => [
                'required'
            ]
        ];
    }
}
