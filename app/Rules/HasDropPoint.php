<?php

namespace App\Rules;

use App\Models\ShippingMethod;
use Illuminate\Contracts\Validation\Rule;
use Illuminate\Support\Arr;

class HasDropPoint implements Rule
{
    private $dropPoint;

    /**
     * Create a new rule instance.
     *
     * @return void
     */
    public function __construct($dropPoint = null)
    {
        $this->dropPoint = $dropPoint;
    }
    /**
     * Determine if the validation rule passes.
     *
     * @param  string  $attribute
     * @param  mixed  $value
     * @return bool
     */
    public function passes($attribute, $value): bool
    {
        $shippingMethod = ShippingMethod::find($value);

        return !$shippingMethod || !(Arr::get($shippingMethod->settings, 'has_drop_points') && is_null($this->dropPoint));
    }

    /**
     * Get the validation error message.
     *
     * @return string
     */
    public function message(): string
    {
        return 'Shipping method requires a drop point';
    }
}
