<?php

namespace App\Http\Requests\Customer;

use App\Http\Requests\FormRequest;
use App\Models\Printer;
use App\Models\ShippingBox;
use App\Rules\BelongsToCustomer;
use Illuminate\Support\Arr;

class UpdateRequest extends FormRequest
{
    public static function validationRules(): array
    {
        $customerId = request()->route('customer')->id;

        $rules = StoreRequest::validationRules();

        $rules['label_printer_id'] = [
            'nullable', 
            new BelongsToCustomer(Printer::class, $customerId)
        ];
        $rules['barcode_printer_id'] = [
            'nullable', 
            new BelongsToCustomer(Printer::class, $customerId)
        ];
        $rules['order_slip_printer_id'] = [
            'nullable', 
            new BelongsToCustomer(Printer::class, $customerId)
        ];
        $rules['packing_slip_printer_id'] = [
            'nullable', 
            new BelongsToCustomer(Printer::class, $customerId)
        ];
        
        $rules['shipping_box_id'][] = new BelongsToCustomer(ShippingBox::class, $customerId);

        Arr::forget($rules, 'parent_customer_id');

        if (!auth()->user()->isAdmin()) {
            Arr::forget($rules, 'allow_child_customers');
        }

        return $rules;
    }
}
