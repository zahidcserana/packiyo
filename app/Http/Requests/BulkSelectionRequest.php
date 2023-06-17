<?php

namespace App\Http\Requests;

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
