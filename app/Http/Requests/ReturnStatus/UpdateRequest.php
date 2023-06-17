<?php

namespace App\Http\Requests\ReturnStatus;

use App\Http\Requests\FormRequest;

class UpdateRequest extends FormRequest
{
    public static function validationRules()
    {
        return [
            'name' => [
                'unique:return_statuses,name,' . parent::$formRequest->route('return_status')->id,
                'required',
                'min:3'
            ],
            'color' => [
                'nullable',
                'string'
            ]
        ];
    }
}
