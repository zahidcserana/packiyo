<?php

namespace App\Http\Requests\ContactInformation;

use App\Http\Requests\FormRequest;

class UpdateRequest extends FormRequest
{
    public static function validationRules()
    {
        return StoreRequest::validationRules();
    }
}
