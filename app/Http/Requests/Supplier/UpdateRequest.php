<?php

namespace App\Http\Requests\Supplier;
use App\Http\Requests\FormRequest;

class UpdateRequest extends FormRequest
{
    public static function validationRules()
    {
        $rules = StoreRequest::validationRules();

        $rules['id'] = [
            'required',
            'exists:suppliers,id,deleted_at,NULL'
        ];

        $rules['product_id'] = [
            'nullable',
            'array'
        ];

        $rules['product_id.*'] = [
            'exists:products,id,deleted_at,NULL'
        ];

        return $rules;
    }
}
