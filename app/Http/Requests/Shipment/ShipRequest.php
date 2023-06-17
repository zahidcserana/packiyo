<?php

namespace App\Http\Requests\Shipment;

use App\Http\Requests\ContactInformation\StoreRequest as ContactInformationStoreRequest;
use App\Http\Requests\FormRequest;
use App\Models\Printer;
use App\Rules\BelongsToCustomer;
use App\Rules\Is3PLCustomer;

class ShipRequest extends FormRequest
{
    public static function validationRules()
    {
        $customerId = static::getInputField('customer_id');

        return array_merge(
            [
                'order_id' => [
                    'sometimes',
                    'exists:orders,id,deleted_at,NULL'
                ],
                'webshipper_shipping_rate_id' => [
                    'sometimes',
                    'integer'
                ],
                'printer_id' => [
                    'nullable',
                    'exists:printers,id,deleted_at,NULL',
                    new BelongsToCustomer(Printer::class, $customerId)
                ],
                'customer_id' => [
                    new Is3PLCustomer()
                ]
            ],

            ShipItemRequest::prefixedValidationRules('order_items.*.'),

            ContactInformationStoreRequest::prefixedValidationRules('contact_information.*.')
        );
    }
}
