<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class PickingBatchItemTableResource extends JsonResource
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

        $resource['qty'] = $this->quantity;
        $resource['qty_picked'] = $this->quantity_picked;
        $resource['picked_time'] = $this->toteOrderItems->first()->picked_at ?? '';

        $resource['order'] = [
            'id' => $this->orderItem->order_id,
            'number' => $this->orderItem->order->number,
            'edit_link' => route('order.edit', ['order' => $this->orderItem->order_id ])
        ];

        $resource['product'] = [
            'id' => $this->orderItem->product_id,
            'sku' => $this->orderItem->sku,
            'edit_link' => route('product.edit', ['product' => $this->orderItem->product_id])
        ];

        $resource['location'] = [
            'id' => $this->location_id,
            'name' => $this->location->name,
            'edit_link' => route('location.edit', ['location' => $this->location_id])
        ];

        $totes = [];
        $pickedLocations = [];

        $this->toteOrderItems->map(function($toteOrderItem) use (&$totes, &$pickedLocations) {
            $totes[] = [
                'id' => $toteOrderItem->tote_id,
                'name' => $toteOrderItem->tote->name . ' (' . $toteOrderItem->quantity_remaining . ')',
                'edit_link' => route('tote.edit', ['tote' => $toteOrderItem->tote_id])
            ];

            $pickedLocations[] = [
                'id' => $toteOrderItem->location_id,
                'name' => $toteOrderItem->location->name . ' (' . $toteOrderItem->quantity . ')',
                'edit_link' => route('location.edit', ['location' => $toteOrderItem->location_id])
            ];
        });

        $resource['totes'] = $totes;
        $resource['picked_locations'] = $pickedLocations;

        return $resource;
    }
}
