<?php

namespace App\Http\Resources\ExportResources;

use Illuminate\Http\Request;

class ShippedItemReportExportResource extends ExportResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  Request  $request
     * @return array
     */
    public function toArray($request)
    {
        return [
            'sku' => $this->orderItem->product->sku,
            'product_name' => $this->orderItem->product->name,
            'order_number' => $this->orderItem->order->number,
            'created_at' => user_date_time($this->created_at, true),
            'qty_ordered' => $this->orderItem->quantity,
            'quantity' => $this->quantity,
            'price' => $this->orderItem->price,
            'price_total' => $this->quantity * $this->orderItem->price,
            'store' => $this->orderItem->order->orderChannel->name ?? '',
            'packer' => $this->shipment->user->contactInformation->name ?? '',
            'tracking_number' => $this->shipmentTracking(),
            'shipping_method' => $this->shipment->shippingMethod->name ?? null,
            'shipping_carrier' => $this->shipment->shippingMethod->shippingCarrier->name ?? null,
        ];
    }

    public static function columns(): array
    {
        return [
            'SKU',
            'Product name',
            'Order number',
            'Created at',
            'Quantity ordered',
            'Quantity',
            'Price',
            'Total price',
            'Store',
            'Packer',
            'Tracking number',
            'Shipping method',
            'Carrier',
        ];
    }

    private function shipmentTracking()
    {
        $shipmentTrackings = $this->shipment->shipmentTrackings;

        if ($shipmentTrackings->count() > 0) {
            $trackingNumbers = $shipmentTrackings->pluck('tracking_number')->toArray();

            return join(' ', $trackingNumbers);
        }

        return null;
    }
}
