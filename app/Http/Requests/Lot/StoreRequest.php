<?php

namespace App\Http\Requests\Lot;

use App\Http\Requests\FormRequest;

class StoreRequest extends FormRequest
{
    public static function validationRules()
    {
        return [
            'name' => [
                'required',
                'min:3',
                'unique:lots,name,$id,id,name,'.\request()->input('name').',supplier_id,'.\request()->input('supplier_id').',product_id,'.\request()->input('product_id')
            ],
            'expiration_date' => [
                'nullable',
                'date'
            ],
            'customer_id' => [
                'required',
                'exists:customers,id'
            ],
            'product_id' => [
                'required',
                'exists:products,id'
            ],
            'supplier_id' => [
                'required',
                'exists:suppliers,id'
            ],
        ];
    }
}
