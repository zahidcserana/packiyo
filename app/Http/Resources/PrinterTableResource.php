<?php

namespace App\Http\Resources;

use Carbon\Carbon;
use Illuminate\Http\Resources\Json\JsonResource;

class PrinterTableResource extends JsonResource
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
        $resource['hostname'] = $this->hostname;
        $resource['name'] = $this->name;
        $resource['user_id'] = $this->user_id;
        $resource['created_at'] = user_date_time($this->created_at);
        $resource['disabled_at'] = is_null($this->disabled_at) ? null : user_date_time($this->disabled_at);

        $resource['link_jobs'] = route('printer.jobs', ['printer' => $this]);
        $resource['link_disable'] = route('printer.disable', ['printer' => $this]);
        $resource['link_enable'] = route('printer.enable', ['printer' => $this]);

        return $resource;
    }
}
