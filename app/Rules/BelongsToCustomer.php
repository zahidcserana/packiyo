<?php

namespace App\Rules;

use App\Models\Customer;
use Illuminate\Contracts\Validation\Rule;

class BelongsToCustomer implements Rule
{
    private $className;
    private $customerIds;
    private $staticValue;

    /**
     * Create a new rule instance.
     *
     * @return void
     */
    public function __construct($className, $customerIds, $staticValue = null)
    {
        if (!is_array($customerIds)) {
            $this->customerIds = [$customerIds];

            $customer = Customer::find($customerIds);

            if ($customer && $customer->parent) {
                $this->customerIds[] = $customer->parent_id;
            }
        } else {
            $this->customerIds = $customerIds;
        }

        $this->className = $className;
        $this->staticValue = $staticValue;
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
        if (!is_null($this->staticValue) && $value == $this->staticValue) {
            return true;
        }

        $object = call_user_func(array($this->className, 'find'), $value);
        return $object && in_array($object->customer_id, $this->customerIds);
    }

    /**
     * Get the validation error message.
     *
     * @return string
     */
    public function message(): string
    {
        return trans(':attribute doesn\'t belong to customer');
    }
}
