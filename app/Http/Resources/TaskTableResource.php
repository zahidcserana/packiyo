<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class TaskTableResource extends JsonResource
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

        $resource['user'] = ['url' => route('user.edit', ['user' => $this->user]), 'name' => $this->user->contactInformation->name];
        $resource['customer'] = ['url' => route('customer.edit', ['customer' => $this->customer]), 'name' => $this->customer->contactInformation->name];
        $resource['task_type'] = $this->taskType->name;
        $resource['notes'] = $this->notes;
        $resource['link_edit'] = route('task.edit', ['task' => $this]);
        $resource['link_delete'] = ['token' => csrf_token(), 'url' => route('task.destroy', ['id' => $this->id, 'task' => $this])];

        return $resource;
    }
}
