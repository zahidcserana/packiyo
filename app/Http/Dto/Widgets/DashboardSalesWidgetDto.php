<?php

namespace App\Http\Dto\Widgets;

class DashboardSalesWidgetDto
{
    public int|null $ordersTotalPrice;
    public int|null $unitsSold;
    public int|null $totalOrders;
    public int|null $avgOrderSize;

    /**\
     * @param int|null $ordersTotalPrice
     * @param int|null $unitsSold
     * @param int|null $totalOrders
     * @param int|null $avgOrderSize
     */
    public function __construct(?int $ordersTotalPrice, ?int $unitsSold, ?int $totalOrders, ?int $avgOrderSize)
    {
        $this->ordersTotalPrice = $ordersTotalPrice;
        $this->unitsSold = $unitsSold;
        $this->totalOrders = $totalOrders;
        $this->avgOrderSize = $avgOrderSize;
    }
}
