<?php

namespace App\Http\Requests\Tote;

use App\Http\Requests\FormRequest;

class PrintBarcodesRequest extends FormRequest
{
    public static function validationRules(): array
    {
        return [
            'totes' => [
                'array',
            ],
            'totes.*' => [
                'required',
                'exists:totes,id',
            ],
        ];
    }
}
