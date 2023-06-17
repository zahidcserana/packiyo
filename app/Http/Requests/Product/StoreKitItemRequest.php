<?php

namespace App\Http\Requests\Product;

use App\Http\Requests\FormRequest;
use App\Http\Requests\Image\ImageRequest;
use App\Models\Product;
use App\Rules\UniqueForCustomer;

class StoreKitItemRequest extends FormRequest
{
    public static function validationRules(): array
    {
        $rules = [
            'kit-quantity' => [
                'sometimes',
            ],
            'kit-quantity.*' => [
                'required',
                'numeric'
            ],
            'kit-items.*' => [
                'sometimes',
                'numeric'
            ]
        ];

        return array_merge_recursive($rules, ImageRequest::prefixedValidationRules('product_images.*.'));
    }
}
