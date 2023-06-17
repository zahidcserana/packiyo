<?php

namespace App\Http\Requests\Printer;

use App\Http\Requests\FormRequest;

class PrinterJobStatusRequest extends FormRequest
{
    public static function validationRules()
    {
        $rules = [
            'status' => [
                'required',
                'string',
            ],
            'job_end' => [
                'required',
                'numeric',
            ]
        ];

        return $rules;
    }
}
