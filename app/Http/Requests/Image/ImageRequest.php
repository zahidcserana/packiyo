<?php

namespace App\Http\Requests\Image;

use App\Http\Requests\FormRequest;

class ImageRequest extends FormRequest
{
    public static function validationRules()
    {
        return [
            'source' => [
                'sometimes',
            ],
            'image' => [
                'sometimes',
                'image',
                'max:8192',
            ]
        ];
    }
}
