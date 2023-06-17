<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class PickerReportTableResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  Request  $request
     * @return array
     */
    public function toArray($request)
    {
        $resource = parent::toArray($request);

        $resource['name'] = $this->user->contactInformation->name;
        $resource['items_count'] = $this->items_count;
        $resource['unique_items_count'] = $this->unique_items_count;
        $resource['orders_count'] = $this->orders_count;

        return $resource;
    }
}
