<?php

namespace App\Http\Dto\Widgets;

class DashboardInfoWidgetPurchasesDto
{
    public int $purchasesOpen;
    public int $purchasesComplete;
    public int $purchasesOpenItems;
    public int $purchasesCompletedItems;

    /**
     * @param int $purchasesOpen
     * @param int $purchasesComplete
     * @param int $purchasesOpenItems
     * @param int $purchasesCompletedItems
     */
    public function __construct(int $purchasesOpen, int $purchasesComplete, int $purchasesOpenItems,int $purchasesCompletedItems,)
    {
        $this->purchasesOpen = $purchasesOpen;
        $this->purchasesComplete = $purchasesComplete;
        $this->purchasesOpenItems = $purchasesOpenItems;
        $this->purchasesCompletedItems = $purchasesCompletedItems;
    }
}
