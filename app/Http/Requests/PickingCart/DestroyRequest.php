<?php

namespace App\Http\Requests\PickingCart;

use App\Http\Requests\FormRequest;

class DestroyRequest extends FormRequest
{
    public static function validationRules(): array
    {
        return [
            'id' => [
                'required',
                'exists:picking_carts,id,deleted_at,NULL'
            ]
        ];
    }
}
