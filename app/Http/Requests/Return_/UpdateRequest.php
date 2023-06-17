<?php

namespace App\Http\Requests\Return_;

use App\Http\Requests\FormRequest;

class UpdateRequest extends FormRequest
{
    public static function validationRules()
    {
        $rules = StoreRequest::validationRules();

        foreach ($rules['number'] as $key => $rule) {
            if (str_contains($rule, 'unique:returns')) {
                unset($rules['number'][$key]);
                break;
            }
        }
        $rules['number'][] = 'exists:returns,number,deleted_at,NULL';

        foreach ($rules['items.*.quantity'] as $key => $rule) {
            if (str_contains($rule, 'min:0')) {
                unset($rules['items.*.quantity'][$key]);
            }
            if (str_contains($rule, 'not_in:0')) {
                unset($rules['items.*.quantity'][$key]);
            }
        }

        $rules['items.*.return_item_id'] = ['sometimes', 'exists:items,id,deleted_at,NULL'];

        return $rules;
    }
}
