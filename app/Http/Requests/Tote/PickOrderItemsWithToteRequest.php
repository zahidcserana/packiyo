<?php

namespace App\Http\Requests\Tote;

use App\Http\Requests\FormRequest;

class PickOrderItemsWithToteRequest extends FormRequest
{
    public static function validationRules(): array
    {
        return [
            'product_barcode' => [
                'required',
                'exists:products,barcode,deleted_at,NULL'
            ],
            'tote_barcode' => [
                'required',
                'exists:totes,barcode,deleted_at,NULL'
            ],
            'location_barcode' => [
                'required',
                'exists:locations,id,deleted_at,NULL'
            ],
            'quantity' => [
                'required',
                'integer'
            ]
        ];
    }
}
