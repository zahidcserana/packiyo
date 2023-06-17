<?php

namespace App\Http\Dto\Filters;

use Illuminate\Contracts\Support\Arrayable;
use Illuminate\Support\Collection;

class ReturnsDataTableDto implements Arrayable
{
    public Collection $returnStatuses;
    public Collection $warehouses;
    public Collection $skus;

    /**
     * @param Collection $returnStatuses
     * @param Collection $warehouses
     * @param Collection $skus
     */
    public function __construct(Collection $returnStatuses, Collection $warehouses, Collection $skus)
    {
        $this->returnStatuses = $returnStatuses;
        $this->warehouses = $warehouses;
        $this->skus = $skus;
    }

    /**
     * @return array
     */
    public function toArray(): array
    {
        return [
            'returnStatuses' => $this->returnStatuses,
            'warehouses' => $this->warehouses,
            'skus' => $this->skus,
        ];
    }
}
