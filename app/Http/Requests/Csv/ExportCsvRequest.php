<?php

namespace App\Http\Requests\Csv;

use App\Http\Requests\FormRequest;

class ExportCsvRequest extends FormRequest
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
