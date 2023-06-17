<?php

namespace App\Http\Dto\Filters;

use Illuminate\Contracts\Support\Arrayable;
use Illuminate\Support\Collection;

class PurchaseOrdersDataTableDto implements Arrayable
{
    public Collection $purchaseOrderStatuses;
    public Collection $warehouses;

    /**
     * @param Collection $purchaseOrderStatuses
     * @param Collection $warehouses
     */
    public function __construct(Collection $purchaseOrderStatuses, Collection $warehouses)
    {
        $this->purchaseOrderStatuses = $purchaseOrderStatuses;
        $this->warehouses = $warehouses;
    }

    /**
     * @return array
     */
    public function toArray(): array
    {
        return [
            'purchaseOrderStatuses' => $this->purchaseOrderStatuses,
            'warehouses' => $this->warehouses,
        ];
    }
}
