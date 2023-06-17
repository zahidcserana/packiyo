<?php

namespace App\Http\Dto\Widgets;

class DashboardInfoWidgetOrdersDto
{
    public int $ordersToday;
    public int $ordersConfirm;
    public int $ordersToShip;
    public int $ordersComplete;

    /**
     * @param int $ordersToday
     * @param int $ordersConfirm
     * @param int $ordersToShip
     * @param int $ordersComplete
     */
    public function __construct(int $ordersToday, int $ordersConfirm, int $ordersToShip,int $ordersComplete,)
    {
        $this->ordersToday = $ordersToday;
        $this->ordersConfirm = $ordersConfirm;
        $this->ordersToShip = $ordersToShip;
        $this->ordersComplete = $ordersComplete;
    }
}
