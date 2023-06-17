<?php

namespace App\Rules;

use App\Models\Order;
use Illuminate\Contracts\Validation\Rule;

class UniqueOrderNumber implements Rule
{
    private $customerId;
    private $orderChannelId;

    /**
     * Create a new rule instance.
     *
     * @return void
     */
    public function __construct($customerId, $orderChannelId)
    {
        $this->customerId = $customerId;
        $this->orderChannelId = $orderChannelId;
    }

    /**
     * Determine if the validation rule passes.
     *
     * @param  string  $attribute
     * @param  mixed  $value
     * @return bool
     */
    public function passes($attribute, $value)
    {
        $splitAttribute = explode('.', $attribute);
        $attribute = $splitAttribute[count($splitAttribute) - 1];

        $exists = Order::where([
            'customer_id' => $this->customerId,
            'order_channel_id' => $this->orderChannelId,
            $attribute => $value
        ])->exists();

        if ($exists) {
            return false;
        }

        return true;
    }

    /**
     * Get the validation error message.
     *
     * @return string
     */
    public function message()
    {
        return trans(':attribute already exists for customer');
    }
}
