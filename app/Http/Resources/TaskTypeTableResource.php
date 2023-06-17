<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class TaskTypeTableResource extends JsonResource
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

        $resource['customer'] = ['url' => route('customer.edit', ['customer' => $this->customer]), 'name' => $this->customer->contactInformation->name];
        $resource['name'] = $this->name;
        $resource['link_edit'] =route('task_type.edit', ['task_type' => $this]);
        $resource['link_delete'] = ['token' => csrf_token(), 'url' => route('task_type.destroy', ['id' => $this->id, 'task_type' => $this])];

        return $resource;
    }
}
