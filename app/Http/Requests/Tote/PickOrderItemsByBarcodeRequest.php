<?php

namespace App\Http\Requests\Tote;

use App\Http\Requests\FormRequest;

class PickOrderItemsByBarcodeRequest extends FormRequest
{
    public static function validationRules(): array
    {
        return [
            'tote_barcode' => [
                'required',
            ],
            'product_barcode' => [
                'required',
            ],
            'location_barcode' => [
                'required',
            ],
            'quantity' => [
                'required',
                'integer'
            ]
        ];
    }
}
