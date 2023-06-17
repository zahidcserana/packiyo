<?php

namespace App\Http\Requests\Csv;

use App\Http\Requests\FormRequest;

class ImportCsvRequest extends FormRequest
{
    public static function validationRules(): array
    {
        return [
            'customer_id' => [
                'required',
                'exists:customers,id'
            ],
            'import_csv' => [
                'required'
            ]
        ];
    }
}
