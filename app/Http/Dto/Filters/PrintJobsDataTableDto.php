<?php

namespace App\Http\Dto\Filters;

use Illuminate\Contracts\Support\Arrayable;
use Illuminate\Support\Collection;

class PrintJobsDataTableDto implements Arrayable
{

    public Collection $printJobs;

    /**
     * @param Collection $printJobs
     */
    public function __construct(Collection $printJobs)
    {
        $this->printJobs = $printJobs;
    }

    /**
     * @return array
     */
    public function toArray(): array
    {
        return [
            'printJobs' => $this->printJobs
        ];
    }
}
