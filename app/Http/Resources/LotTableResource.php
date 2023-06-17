<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class LotTableResource extends JsonResource
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
        $resource['product'] = ['name' => $this->product->name, 'sku' => $this->product->sku, 'url' => route('product.edit', ['product' => $this->product])];
        $resource['link_edit'] = route('lot.edit', [ 'lot' => $this ]);
        $resource['link_delete'] = ['token' => csrf_token(), 'url' => route('lot.destroy', ['id' => $this->id, 'lot' => $this])];
        $resource['expiration_date'] =  user_date_time($this->expiration_date);

        return $resource;
    }
}
