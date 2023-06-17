<?php

namespace App\Http\Resources\ExportResources;

use Illuminate\Http\Request;

class SerialNumberReportExportResource extends ExportResource
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
            'serial_number' => $this->serial_number,
            'date_shipped' => user_date_time($this->package->shipment->created_at, true),
        ];
    }

    public static function columns(): array
    {
        return [
            'SKU',
            'Product name',
            'Order number',
            'Serial number',
            'Date shipped',
        ];
    }
}
