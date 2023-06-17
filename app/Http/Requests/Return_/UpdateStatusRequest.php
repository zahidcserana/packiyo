<?php

namespace App\Http\Requests\Return_;

use App\Http\Requests\FormRequest;
use App\Rules\ExistsOrStaticValue;

class UpdateStatusRequest extends FormRequest
{
    public static function validationRules(): array
    {
        return [
            'return_status_id' => [
                'required',
                new ExistsOrStaticValue('return_statuses', 'id', 'pending')
            ]
        ];
    }
}
