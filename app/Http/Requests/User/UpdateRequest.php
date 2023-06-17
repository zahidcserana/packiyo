<?php

namespace App\Http\Requests\User;

use App\Http\Requests\ContactInformation\UpdateRequest as ContactInformationUpdateRequest;
use App\Http\Requests\FormRequest;
use App\Models\Printer;
use App\Rules\BelongsToCustomer;

class UpdateRequest extends FormRequest
{
    public static function validationRules()
    {
        $customerIds = auth()->user()->customers()->pluck('customers.id')->toArray();

        $rules = StoreRequest::validationRules(false);

        unset($rules['customer_id']);

        $rules['email'] = ['required', 'email', 'unique:users,email,' . self::$formRequest->user->id];
        $rules['password'] = ['nullable'];
        $rules['password_confirmation'] = ['nullable', 'required_with:password', 'same:password'];
        $rules['exclude_single_line_orders'] = ['nullable'];

        $rules['label_printer_id'] = [
            new BelongsToCustomer(Printer::class, $customerIds)
        ];

        $rules['barcode_printer_id'] = [
            new BelongsToCustomer(Printer::class, $customerIds)
        ];

        $rules['order_slip_printer_id'] = [
            new BelongsToCustomer(Printer::class, $customerIds)
        ];

        $rules['packing_slip_printer_id'] = [
            new BelongsToCustomer(Printer::class, $customerIds)
        ];

        return array_merge_recursive(
            $rules,
            ContactInformationUpdateRequest::prefixedValidationRules('contact_information.')
        );
    }
}
