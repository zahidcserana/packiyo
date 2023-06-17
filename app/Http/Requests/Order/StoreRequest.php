<?php

namespace App\Http\Requests\Order;

use App\Http\Requests\ContactInformation\StoreRequest as ContactInformationStoreRequest;
use App\Http\Requests\FormRequest;
use App\Http\Requests\OrderItem\StoreRequest as OrderItemStoreRequest;
use App\Models\Order;
use App\Models\OrderChannel;
use App\Models\OrderStatus;
use App\Rules\BelongsToCustomer;
use App\Rules\ExistsOrStaticValue;
use App\Rules\UniqueForCustomer;
use App\Rules\UniqueOrderNumber;

class StoreRequest extends FormRequest
{
    public static function validationRules()
    {
        $customerId = static::getInputField('customer_id');
        $orderChannelId = static::getInputField('order_channel_id');

        $rules = [
            'customer_id' => [
                'sometimes',
                'required',
                'exists:customers,id,deleted_at,NULL'
            ],
            'order_channel_id' => [
                'nullable',
                new BelongsToCustomer(OrderChannel::class, $customerId)
            ],
            'external_id' => [
                'sometimes',
                new UniqueForCustomer(Order::class, $customerId)
            ],
            'order_status_id' => [
                'sometimes',
                new ExistsOrStaticValue('order_statuses', 'id', 'pending'),
                new BelongsToCustomer(OrderStatus::class, $customerId, 'pending')
            ],
            'number' => [
                'sometimes',
                'string',
                new UniqueOrderNumber($customerId, $orderChannelId)
            ],
            'ordered_at' => [
                'sometimes',
                'date_format:Y-m-d H:i:s'
            ],
            'required_shipping_date_at' => [
                'sometimes',
                'nullable',
                'date_format:Y-m-d'
            ],
            'shipping_date_before_at' => [
                'sometimes',
                'nullable',
                'date_format:Y-m-d'
            ],
            'slip_note' => [
                'sometimes'
            ],
            'packing_note' => [
                'sometimes'
            ],
            'internal_note' => [
                'sometimes'
            ],
            'gift_note' => [
                'sometimes'
            ],
            'append_slip_note' => [
                'sometimes'
            ],
            'append_packing_note' => [
                'sometimes'
            ],
            'append_internal_note' => [
                'sometimes'
            ],
            'append_gift_note' => [
                'sometimes'
            ],
            'tags' => [
                'sometimes',
            ],
            'fraud_hold' => [
                'sometimes',
                'boolean'
            ],
            'allocation_hold' => [
                'sometimes',
                'boolean'
            ],
            'address_hold' => [
                'sometimes',
                'boolean'
            ],
            'payment_hold' => [
                'sometimes',
                'boolean'
            ],
            'operator_hold' => [
                'sometimes',
                'boolean'
            ],
            'priority' => [
                'sometimes',
                'integer'
            ],
            'allow_partial' => [
                'sometimes',
                'integer'
            ],
            'shipping' => [
                'sometimes',
                'numeric'
            ],
            'tax' => [
                'sometimes',
                'numeric'
            ],
            'discount' => [
                'sometimes',
                'numeric'
            ],
            'shipping_method_id' => [
                'sometimes',
                'required_without:shipping_method_name',
                'nullable',
                new ExistsOrStaticValue('shipping_methods', 'id', 'dummy'),
            ],
            'shipping_method_name' => [
                'sometimes',
                'required_without:shipping_method_id'
            ],
            'shipping_method_code' => [
                'sometimes'
            ],
            'order_items' => [
                'required',
                'array',
            ],
            'order_items.*.cancelled' => [
                'sometimes',
            ],
            'order_items.*.is_kit_item' => [
                'sometimes',
            ],
            'order_items.*.child_count' => [
                'sometimes',
            ],
            'order_items.*.child_quantity' => [
                'sometimes',
            ],
            'order_items.*.parent_product_id' => [
                'sometimes',
            ],
            'currency_id' => [
                'nullable',
                'exists:currencies,id'
            ],
            'currency_code' => [
                'nullable',
                'exists:currencies,code'
            ],
            'custom_invoice_url' => [
                'nullable'
            ]
        ];

        OrderItemStoreRequest::$customerId = $customerId;

        $rules = array_merge_recursive($rules, OrderItemStoreRequest::prefixedValidationRules('order_items.*.'));

        $rules = array_merge_recursive($rules, ContactInformationStoreRequest::prefixedValidationRules('shipping_contact_information.'));

        $billingInformationRules = ContactInformationStoreRequest::prefixedValidationRules('billing_contact_information.');

        foreach ($billingInformationRules as $attribute => $billingContactInformationRules) {
            foreach ($billingContactInformationRules as $key => $rule) {
                if (str_starts_with($rule, 'required')) {
                    $billingInformationRules[$attribute][$key] = 'nullable';
                }
            }
        }

        return array_merge_recursive($rules, $billingInformationRules);
    }
}
