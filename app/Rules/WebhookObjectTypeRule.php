<?php

namespace App\Rules;

use Illuminate\Contracts\Validation\Rule;
use App\Models\Webhook;

class WebhookObjectTypeRule implements Rule
{
    /**
     * Determine if the validation rule passes.
     *
     * @param  string  $attribute
     * @param  mixed  $value
     * @return bool
     */
    public function passes($attribute, $value)
    {
        return in_array($value, Webhook::WEBHOOK_OBJECT_TYPES);
    }

    /**
     * Get the validation error message.
     *
     * @return string
     */
    public function message()
    {
        return 'The object type is invalid.';
    }
}
