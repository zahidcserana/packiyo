<?php

namespace App\Http\Dto\Widgets;


class DashboardInfoWidgetDto
{
    public DashboardInfoWidgetOrdersDto $orders;
    public DashboardInfoWidgetProductsDto $products;
    public DashboardInfoWidgetShipmentsDto $shipments;
    public DashboardInfoWidgetPurchasesDto $purchases;

    /**
     * @param DashboardInfoWidgetOrdersDto $orders
     * @param DashboardInfoWidgetProductsDto $products
     * @param DashboardInfoWidgetShipmentsDto $shipments
     * @param DashboardInfoWidgetPurchasesDto $purchases
     */
    public function __construct(DashboardInfoWidgetOrdersDto $orders, DashboardInfoWidgetProductsDto $products, DashboardInfoWidgetShipmentsDto $shipments, DashboardInfoWidgetPurchasesDto $purchases)
    {
        $this->orders = $orders;
        $this->products = $products;
        $this->shipments = $shipments;
        $this->purchases = $purchases;
    }
}
