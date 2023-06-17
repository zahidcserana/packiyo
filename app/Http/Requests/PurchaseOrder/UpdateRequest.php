<?php

namespace App\Http\Requests\PurchaseOrder;

use App\Http\Requests\FormRequest;

class UpdateRequest extends FormRequest
{
    public static function validationRules()
    {
        $rules = StoreRequest::validationRules();

        unset($rules['external_id']);
        unset($rules['number']);

        foreach ($rules['purchase_order_items.*.quantity'] as $key => $rule) {
            if (strpos($rule, 'min:0') !== false) {
                unset($rules['purchase_order_items.*.quantity'][$key]);
            }
            if (strpos($rule, 'not_in:0') !== false) {
                unset($rules['f.*.quantity'][$key]);
            }
        }

        $rules['purchase_order_items.*.purchase_order_item_id'] = ['sometimes', 'exists:purchase_order_items,id'];

        return $rules;
    }
}
