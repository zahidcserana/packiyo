<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class OrderStatusTableResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  Request  $request
     * @return array
     */
    public function toArray($request): array
    {
        unset($resource);

        $resource['id'] = $this->id;
        $resource['name'] = $this->name;
        $resource['customer'] = ['name' => $this->customer->contactInformation->name, 'url' => route('customer.edit', ['customer' => $this->customer])];
        $resource['link_edit'] = route('order_status.edit', [ 'order_status' => $this ]);
        $resource['link_delete'] = ['token' => csrf_token(), 'url' => route('order_status.destroy', ['id' => $this->id, 'order_status' => $this])];
        $resource['color'] = $this->color;

        return $resource;
    }
}
