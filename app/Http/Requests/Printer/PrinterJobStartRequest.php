<?php

namespace App\Http\Requests\Printer;

use App\Http\Requests\FormRequest;

class PrinterJobStartRequest extends FormRequest
{
    public static function validationRules()
    {
        $rules = [
            'job_id_system' => [
                'required',
                'integer',
            ]
        ];

        return $rules;
    }
}
