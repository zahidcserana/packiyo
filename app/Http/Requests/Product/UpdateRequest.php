<?php

namespace App\Http\Requests\Product;

use App\Http\Requests\FormRequest;

class UpdateRequest extends FormRequest
{
    public static function validationRules(): array
    {
        $rules = StoreRequest::validationRules();

        $rules['id'] = [
            'nullable'
        ];

        if (isset($rules['customer_id'])) {
            unset($rules['customer_id']);
        }

        $rules['sku'] = ['sometimes'];
        $rules['name'] = ['sometimes'];

        foreach ($rules['price'] as $key => $rule) {
            if (str_contains($rule, 'required')) {
                $rules['price'][$key] = 'nullable';
                break;
            }
        }

        return $rules;
    }

    public function messages(): array
    {
        return [
            'product_lots.*.id.required_unless' => 'The Lot Name field is required because this product has lot tracking enabled.'
        ];
    }
}
