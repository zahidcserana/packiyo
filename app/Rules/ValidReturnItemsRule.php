<?php

namespace App\Rules;

use App\Models\OrderItem;
use Illuminate\Contracts\Validation\DataAwareRule;
use Illuminate\Contracts\Validation\Rule;

class ValidReturnItemsRule implements Rule, DataAwareRule
{
    protected $data = [];

    public function setData($data)
    {
        $this->data = $data;

        return $this;
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
        $items = $this->data['items'] ?? [];
        $items = empty($items) && isset($this->data['order_items']) ? $this->data['order_items'] : $items;

        $items = array_filter($items, function ($item) {
            return isset($item['is_returned']) && $item['quantity'] > 0;
        });

        if (!count($items)) {
            return false;
        }

        if (isset($this->data['order_id'])) {
            $orderItems = OrderItem::selectRaw('SUM(`quantity_shipped`) AS `available_for_return`, product_id')
                ->where('order_id', $this->data['order_id'])
                ->groupBy('product_id')
                ->get()
                ->keyBy('product_id');

            foreach ($items as $key => $item) {
                if (empty($orderItems[$key]) || $orderItems[$key]['available_for_return'] < $item['quantity']) {
                    return false;
                }
            }
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
        return __('You must choose items that were shipped.');
    }
}
