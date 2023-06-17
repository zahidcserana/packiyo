<?php

namespace App\Http\Requests\Order;

use App\Http\Requests\FormRequest;

class UpdateRequest extends FormRequest
{
    public static function validationRules(): array
    {
        $rules = StoreRequest::validationRules();

        // TODO: also add BelongsTo
        $rules['id'] = [
            'nullable'
        ];

        unset($rules['external_id'], $rules['number']);

        foreach ($rules['order_items.*.quantity'] as $key => $rule) {
            if (str_contains($rule, 'min:0')) {
                unset($rules['order_items.*.quantity'][$key]);
            }
            if (str_contains($rule, 'not_in:0')) {
                unset($rules['order_items.*.quantity'][$key]);
            }
        }

        $rules['order_items.*.order_item_id'] = ['sometimes', 'exists:order_items,id'];

        foreach ($rules['order_status_id'] as $key => $rule) {
            if (is_string($rule) && str_contains($rule, 'required')) {
                $rules['order_status_id'][$key] = 'sometimes';
            }
        }

        foreach ($rules['order_items'] as $key => $rule) {
            if (is_string($rule) && str_contains($rule, 'required')) {
                $rules['order_items'][$key] = 'sometimes';
            }
        }

        return $rules;
    }
}
