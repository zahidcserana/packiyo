<?php

namespace App\Http\Requests\Location;

use App\Http\Requests\FormRequest;

class UpdateRequest extends FormRequest
{
    public static function validationRules(): array
    {
        $rules = StoreRequest::validationRules();

        $rules['id'] = ['required', 'exists:locations,id,deleted_at,NULL'];

        foreach ($rules['location_product.*.quantity_on_hand'] as $key => $rule) {
            if (strpos($rule, 'min:0') !== false) {
                unset($rules['location_product.*.quantity_on_hand'][$key]);
            }
            if (strpos($rule, 'not_in:0') !== false) {
                unset($rules['location_product.*.quantity_on_hand'][$key]);
            }
        }

        $rules['location_product.*.location_product_id'] = ['sometimes', 'exists:location_product,id'];
        $rules['location_product.*.delete'] = ['sometimes'];

        return $rules;
    }

    public function messages(): array
    {
        return [
            'name.required' => 'The name field is required',
            'location_product.0.product_id.*' => 'No product was selected'
        ];
    }
}
