<?php

namespace App\Http\Dto\Widgets;

class DashboardInfoWidgetShipmentsDto
{
    public int $shipmentsToday;
    public int $shipmentsYesterday;
    public int $shipmentsLastWeek;
    public int $shipmentsLastMonth;

    /**\
     * @param int $shipmentsToday
     * @param int $shipmentsYesterday
     * @param int $shipmentsLastWeek
     * @param int $shipmentsLastMonth
     */
    public function __construct(int $shipmentsToday, int $shipmentsYesterday, int $shipmentsLastWeek, int $shipmentsLastMonth)
    {
        $this->shipmentsToday = $shipmentsToday;
        $this->shipmentsYesterday = $shipmentsYesterday;
        $this->shipmentsLastWeek = $shipmentsLastWeek;
        $this->shipmentsLastMonth = $shipmentsLastMonth;
    }
}
