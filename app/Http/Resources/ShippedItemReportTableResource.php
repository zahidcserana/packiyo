<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ShippedItemReportTableResource extends JsonResource
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

        $resource['created_at'] = user_date_time($this->created_at, true);
        $resource['qty_ordered'] = $this->orderItem->quantity;
        $resource['quantity'] = $this->quantity;
        $resource['price'] = $this->orderItem->price;
        $resource['price_total'] = $this->quantity * $this->orderItem->price;
        $resource['store'] = $this->orderItem->order->orderChannel->name ?? '';
        $resource['packer'] = $this->shipment->user->contactInformation->name ?? '';
        $resource['tracking_number'] = $this->shipmentTracking();
        $resource['shipping_method'] = $this->shipment->shippingMethod->name ?? null;
        $resource['shipping_carrier'] = $this->shipment->shippingMethod->shippingCarrier->name ?? null;

        $resource['order'] = [
            'id' => $this->orderItem->order->id,
            'number' => $this->orderItem->order->number,
            'ordered_at' => user_date_time($this->orderItem->order->ordered_at, true),
            'url' => route('order.edit', ['order' => $this->orderItem->order]),
        ];

        $resource['product'] = [
            'id' => $this->orderItem->product->id,
            'sku' => $this->orderItem->product->sku,
            'name' => $this->orderItem->product->name,
            'url' => route('product.edit', ['product' => $this->orderItem->product]),
        ];

        return $resource;
    }

    private function shipmentTracking()
    {
        $trackingNumbers = '';
        $shipmentTrackings = $this->shipment->shipmentTrackings;

        if (!is_null($shipmentTrackings)) {
            foreach ($shipmentTrackings as $tracking) {
                $trackingNumbers .= '<a href="' . $tracking->tracking_url . '" target="_blank" class="text-neutral-text-gray">' . $tracking->tracking_number . '</a><br/>';
            }
        }

        return $trackingNumbers;
    }
}
