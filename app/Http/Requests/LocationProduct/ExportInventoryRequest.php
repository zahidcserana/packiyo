<?php

namespace App\Http\Requests\LocationProduct;

use App\Http\Requests\FormRequest;

class ExportInventoryRequest extends FormRequest
{
    public static function validationRules(): array
    {
        return [
            'search' => [
                'nullable'
            ]
        ];
    }
}
