<?php

namespace App\Http\Dto\Filters;

use Illuminate\Contracts\Support\Arrayable;
use Illuminate\Support\Collection;

class LocationsDataTableDto implements Arrayable
{
    public Collection $warehouses;
    /**
     * @param Collection $warehouses
     */
    public function __construct(Collection $warehouses, Collection $location_types)
    {
        $this->warehouses = $warehouses;
        $this->location_types = $location_types;
    }

    /**
     * @return array
     */
    public function toArray(): array
    {
        return [
            'warehouses' => $this->warehouses,
            'location_types' => $this->location_types
        ];
    }
}
