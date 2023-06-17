<?php

namespace App\Http\Dto\Filters;

use Illuminate\Contracts\Support\Arrayable;
use Illuminate\Support\Collection;

class InventoryChangeLogDataTableDto implements Arrayable
{
    public Collection $customers;
    public Collection $warehouses;
    public array $reasons;
    public Collection $users;

    /**
     * @param Collection $customers
     * @param Collection $warehouses
     * @param array $reasons
     * @param Collection $users
     */
    public function __construct(Collection $customers, Collection $warehouses, array $reasons, Collection $users)
    {
        $this->customers = $customers;
        $this->warehouses = $warehouses;
        $this->reasons = $reasons;
        $this->users = $users;
    }

    /**
     * @return array
     */
    public function toArray(): array
    {
        return [
            'customers' => $this->customers,
            'warehouses' => $this->warehouses,
            'reasons' => $this->reasons,
            'users' => $this->users
        ];
    }
}
