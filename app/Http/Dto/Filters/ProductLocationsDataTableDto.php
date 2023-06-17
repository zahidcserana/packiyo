<?php

namespace App\Http\Dto\Filters;

use Illuminate\Contracts\Support\Arrayable;
use Illuminate\Support\Collection;

class ProductLocationsDataTableDto implements Arrayable
{
    public Collection $warehouses;
    /**
     * @param Collection $warehouses
     */
    public function __construct(Collection $warehouses)
    {
        $this->warehouses = $warehouses;
    }

    /**
     * @return array
     */
    public function toArray(): array
    {
        return [
            'warehouses' => $this->warehouses,
        ];
    }
}
