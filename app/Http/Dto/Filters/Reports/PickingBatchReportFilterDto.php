<?php

namespace App\Http\Dto\Filters\Reports;

use Illuminate\Contracts\Support\Arrayable;
use Illuminate\Support\Collection;

class PickingBatchReportFilterDto implements Arrayable
{
    public Collection $users;

    /**
     * @param Collection $shippingCarriers
     */
    public function __construct(Collection $users)
    {
        $this->users = $users;
    }

    /**
     * @return array
     */
    public function toArray(): array
    {
        return [
            'users' => $this->users
        ];
    }
}
