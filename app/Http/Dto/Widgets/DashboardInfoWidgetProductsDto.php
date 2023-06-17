<?php

namespace App\Http\Dto\Widgets;

class DashboardInfoWidgetProductsDto
{
    public int $productUniqueOrders;
    public int $productBackordered;
    public int $productPieces;
    public int $productUniqueSkus;

    /**
     * @param int $productUniqueOrders
     * @param int $productBackordered
     * @param int $productPieces
     * @param int $productUniqueSkus
     */
    public function __construct(int $productUniqueOrders, int $productBackordered, int $productPieces,int $productUniqueSkus,)
    {
        $this->productUniqueOrders = $productUniqueOrders;
        $this->productBackordered = $productBackordered;
        $this->productPieces = $productPieces;
        $this->productUniqueSkus = $productUniqueSkus;
    }
}
