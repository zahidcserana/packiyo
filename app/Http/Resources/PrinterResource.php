<?php

namespace App\Http\Resources;

use Carbon\Carbon;
use Illuminate\Http\Resources\Json\JsonResource;

class PrinterResource extends JsonResource
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

        $jobs = [];
        foreach ($this->printJob as $job){
            $jobs[] = [

                'id' => $job->id,
                'file' => route('shipment.label', ['shipment' => $job->object->id, 'shipmentLabel' => $job->object->shipmentLabels[0]['id']]),
                'status' => $job->status,
                'job_start' => $job->job_start,
                'job_end' => $job->job_end,
                'job_id_system' => $job->job_id_system
            ];
        }

        $resource['id'] = $this->id;
        $resource['name'] = $this->name;
        $resource['status'] = $this->status;
        $resource['user_id'] = $this->user_id;
        $resource['created_at'] = user_date_time($this->created_at);
        $resource['jobs'] = $jobs;

        return $resource;
    }
}
