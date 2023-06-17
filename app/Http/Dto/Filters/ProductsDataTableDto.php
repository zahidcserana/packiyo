<?php

namespace App\Http\Dto\Filters;

use Illuminate\Contracts\Support\Arrayable;
use Illuminate\Support\Collection;

class ProductsDataTableDto implements Arrayable
{
    public Collection $suppliers;
    public Collection $warehouses;

    /**
     * @param Collection $suppliers
     * @param Collection $warehouses
     */
    public function __construct(Collection $suppliers, Collection $warehouses)
    {
        $this->suppliers = $suppliers;
        $this->warehouses = $warehouses;
    }

    /**
     * @return array
     */
    public function toArray(): array
    {
        return [
            'suppliers' => $this->suppliers,
            'warehouses' => $this->warehouses,
        ];
    }
}
