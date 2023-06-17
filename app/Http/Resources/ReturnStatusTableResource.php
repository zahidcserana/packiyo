<?php

namespace App\Http\Resources;

use Carbon\Carbon;
use Illuminate\Http\Resources\Json\JsonResource;

class ReturnStatusTableResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array
     */
    public function toArray($request)
    {
        unset($resource);

        $resource['id'] =  $this->id;
        $resource['name'] =  $this->name;
        $resource['created_at'] = $this->created_at ? user_date_time($this->created_at) : null;
        $resource['customer'] = ['name' => $this->customer->contactInformation->name, 'url' => route('customer.edit', ['customer' => $this->customer])];
        $resource['link_edit'] = route('return_status.edit', [ 'return_status' => $this ]);
        $resource['link_delete'] = ['token' => csrf_token(), 'url' => route('return_status.destroy', ['id' => $this->id, 'return_status' => $this])];
        $resource['color'] =  $this->color;

        return $resource;
    }
}
