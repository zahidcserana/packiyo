<?php

namespace App\Http\Requests\PickingCart;

use App\Http\Requests\FormRequest;

class UpdateRequest extends FormRequest
{
    public static function validationRules(): array
    {
        return StoreRequest::validationRules();
    }
}
