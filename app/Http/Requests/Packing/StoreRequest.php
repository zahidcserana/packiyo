<?php

namespace App\Http\Requests\Packing;

use App\Http\Requests\ContactInformation\StoreRequest as ContactInformationStoreRequest;
use App\Http\Requests\FormRequest;
use App\Http\Requests\Shipment\ShipItemRequest;
use App\Models\Printer;
use App\Rules\BelongsToCustomer;
use App\Rules\ExistsOrStaticValue;
use App\Rules\HasDropPoint;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Support\Facades\Log;

class StoreRequest extends FormRequest
{
    public static function validationRules(): array
    {
        $customerId = static::getInputField('customer_id');
        $dropPoint = static::getInputField('drop_point_id');

        $contactInformationRules = ContactInformationStoreRequest::prefixedValidationRules('shipping_contact_information.');

        foreach ($contactInformationRules as $attribute => $rule) {
            if (str_contains($attribute, 'zip') || str_contains($attribute, 'country_id')) {
                $contactInformationRules[$attribute] = [
                    'required'
                ];
            }
        }

        return array_merge(
            [
                'shipping_method_id' => [
                    'required',
                    new ExistsOrStaticValue('shipping_methods', 'id', 'dummy'),
                    new HasDropPoint($dropPoint)
                ],
                'packing_state' => [
                    'required',
                    'string'
                ],
                'printer_id' => [
                    'nullable',
                    'exists:printers,id,deleted_at,NULL',
                    new BelongsToCustomer(Printer::class, $customerId)
                ],
                'drop_point_id' => [
                    'nullable'
                ]
            ],
            ShipItemRequest::prefixedValidationRules('order_items.*.'),
            $contactInformationRules
        );
    }

    public function messages(): array
    {
        return [
            'shipping_contact_information.country_id.required' => 'The country field is required for the shipping contact information',
            'shipping_contact_information.zip.required' => 'ZIP code is required for the shipping contact information',
            'drop_point_id.*' => 'Shipping method requires a drop point'
        ];
    }

    protected function failedValidation(Validator $validator)
    {
        Log::info(__CLASS__, request()->all());

        parent::failedValidation($validator);
    }
}
