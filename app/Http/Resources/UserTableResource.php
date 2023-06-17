<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class UserTableResource extends JsonResource
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

        $resource['name'] = $this->contactInformation->name;
        $resource['email'] = $this->email;
        $resource['status'] = $this->status;
        $resource['pin'] = $this->pin;
        $resource['link_edit'] = route('user.edit', ['user' => $this]);
        $resource['link_delete'] = ['token' => csrf_token(), 'url' => route('user.destroy', ['id' => $this->id, 'user' => $this])];

        return $resource;
    }
}
