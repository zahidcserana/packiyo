<?php

namespace App\Http\Requests\Return_;

use App\Http\Requests\FormRequest;

class BulkEditRequest extends FormRequest
{
    public static function validationRules(): array
    {
        return [
            'ids' => [
                'required'
            ],
            'tags' => [
                'sometimes',
                'array'
            ]
        ];
    }

    public function messages(): array
    {
        return [
            'tags.array' => 'You must add at least one tag'
        ];
    }
}
