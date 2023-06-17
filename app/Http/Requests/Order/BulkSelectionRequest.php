<?php

namespace App\Http\Requests\Order;

use App\Http\Requests\FormRequest;

class BulkSelectionRequest extends FormRequest
{
    public static function validationRules(): array
    {
        return [
            'ids' => [
                'required'
            ]
        ];
    }
}
