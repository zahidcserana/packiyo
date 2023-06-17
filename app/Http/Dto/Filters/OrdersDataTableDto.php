<?php

namespace App\Http\Dto\Filters;

use Illuminate\Contracts\Support\Arrayable;
use Illuminate\Support\Collection;

class OrdersDataTableDto implements Arrayable
{
    public Collection $customers;
    public Collection $orderStatuses;
    public Collection $shippingCarriers;
    public Collection $shippingMethods;

    /**
     * @param Collection $customers
     * @param Collection $orderStatuses
     * @param Collection $shippingCarriers
     * @param Collection $shippingMethods
     */
    public function __construct(Collection $customers, Collection $orderStatuses, Collection $shippingCarriers, Collection $shippingMethods)
    {
        $this->customers = $customers;
        $this->orderStatuses = $orderStatuses;
        $this->shippingCarriers = $shippingCarriers;
        $this->shippingMethods = $shippingMethods;
    }

    /**
     * @return array
     */
    public function toArray(): array
    {
        return [
            'customers' => $this->customers,
            'order_statuses' => $this->orderStatuses,
            'shipping_carriers' => $this->shippingCarriers,
            'shipping_methods' => $this->shippingMethods
        ];
    }
}
