<?php

namespace App\Http\Dto\Filters\Reports;

use Illuminate\Contracts\Support\Arrayable;
use Illuminate\Support\Collection;

class ShipmentReportFilterDto implements Arrayable
{
    public Collection $shippingCarriers;
    public Collection $shippingMethods;

    /**
     * @param Collection $shippingCarriers
     * @param Collection $shippingMethods
     */
    public function __construct(Collection $shippingCarriers, Collection $shippingMethods)
    {
        $this->shippingCarriers = $shippingCarriers;
        $this->shippingMethods = $shippingMethods;
    }

    /**
     * @return array
     */
    public function toArray(): array
    {
        return [
            'shipping_carriers' => $this->shippingCarriers,
            'shipping_methods' => $this->shippingMethods
        ];
    }
}
