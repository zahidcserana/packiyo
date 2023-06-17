<?php

namespace App\Http\Resources;

use Carbon\Carbon;
use Illuminate\Http\Resources\Json\JsonResource;

class PrintJobTableResource extends JsonResource
{
    /**
     * Transform the resource collection into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array
     */
    public function toArray($request)
    {
        unset($resource);

        $resource['id'] = $this->id;
        $resource['file'] = $this->url;
        $resource['status'] = $this->status;
        $resource['user'] = $this->user->contactInformation->name;
        $resource['printer'] = $this->printer->name;

        $resource['job_start']  = is_null($this->job_start) ? '' : Carbon::parse($this->job_start)->format('M d Y H:i');
        $resource['job_end']    = is_null($this->job_end) ? '' :  Carbon::parse($this->job_end)->format('M d Y H:i');

        $resource['link_repeat'] = route('printer.job.repeat', ['printJob' => $this]);

        return $resource;
    }
}
