<?php

namespace App\Http\Dto\Filters;

use Illuminate\Contracts\Support\Arrayable;
use Illuminate\Support\Collection;

class PrintersDataTableDto implements Arrayable
{

    public Collection $printers;

    /**
     * @param Collection $printers
     */
    public function __construct(Collection $printers)
    {
        $this->printers = $printers;
    }

    /**
     * @return array
     */
    public function toArray(): array
    {
        return [
            'printers' => $this->printers
        ];
    }
}
