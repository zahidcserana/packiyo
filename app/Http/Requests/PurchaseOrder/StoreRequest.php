<?php

namespace App\Http\Requests\PurchaseOrder;

use App\Http\Requests\FormRequest;
use App\Http\Requests\PurchaseOrderItem\StoreRequest as PurchaseOrderItemStoreRequest;
use App\Models\PurchaseOrder;
use App\Models\Supplier;
use App\Models\Warehouse;
use App\Rules\BelongsToCustomer;
use App\Rules\UniqueForCustomer;

class StoreRequest extends FormRequest
{
    public static function validationRules()
    {
        $customerId = static::getInputField('customer_id');

        $rules = [
            'customer_id' => [
                'required',
                'exists:customers,id,deleted_at,NULL'
            ],
            'external_id' => [
                'sometimes',
                'distinct'
            ],
            'warehouse_id' => [
                'required',
                'exists:warehouses,id,deleted_at,NULL',
                new BelongsToCustomer(Warehouse::class, $customerId)
            ],
            'supplier_id' => [
                'required',
                'exists:suppliers,id,deleted_at,NULL',
                new BelongsToCustomer(Supplier::class, $customerId)
            ],
            'number' => [
                'nullable',
                new UniqueForCustomer(PurchaseOrder::class, $customerId)
            ],
            'ordered_at' => [
                'sometimes'
            ],
            'expected_at' => [
                'sometimes'
            ],
            'delivered_at' => [
                'sometimes'
            ],
            'notes' => [
                'sometimes',
                'string'
            ],
            'priority' => [
                'sometimes',
                'integer'
            ],
            'purchase_order_items' => [
                'required',
                'array',
                'min:1'
            ],
            'tracking_number' => [
                'nullable'
            ],
            'tracking_url' => [
                'nullable'
            ],
            'tags' => [
                'sometimes'
            ],
        ];

        return array_merge_recursive($rules, PurchaseOrderItemStoreRequest::prefixedValidationRules('purchase_order_items.*.'));
    }

    public function messages(): array
    {
        return [
            'purchase_order_items.required' => 'At least one product needs to be added',
            'warehouse_id.required' => 'The Warehouse field is required',
            'supplier_id.required' => 'The Vendor field is required',
            'number.required' => 'The PO Number field is required'
        ];
    }
}
