<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class InventoryLogTableResource extends JsonResource
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
        $resource['customer'] = [
            'id' => $this->product->customer->id,
            'name' => $this->product->customer->contactInformation->name
        ];
        $resource['created_at'] = user_date_time($this->created_at, true);
        $resource['warehouse'] = $this->location->warehouse->contactInformation;
        $resource['sku'] = $this->product->sku;
        $resource['location'] = $this->location->name;
        $resource['previous_on_hand'] = $this->previous_on_hand;
        $resource['new_on_hand'] = $this->new_on_hand;
        $resource['reason'] = $this->getReasonText();

        $resource['user'] =  [
            'url'=> route('user.edit',['user' => $this->user]),
            'name' => $this->user->contactInformation->name
        ];
        $resource['product'] = [
            'url' => '<a href="' . route('product.edit', ['product' => $this->product]) . '" data-id="' . $this->product->id . '" type="button" class="editIcon">'.$this->product->name.'</a>',
            'name' =>  $this->product->name
        ];
        $resource['quantity'] = $this->quantity;

        return $resource;
    }
}
