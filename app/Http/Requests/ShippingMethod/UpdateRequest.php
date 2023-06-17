<?php

namespace App\Http\Requests\ShippingMethod;

use App\Http\Requests\FormRequest;

class UpdateRequest extends FormRequest
{
    public static function validationRules()
    {
        return [
            'tags' => 'sometimes',
        ];
    }
}
