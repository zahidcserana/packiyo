<?php

namespace App\Http\Resources\ExportResources;

use Illuminate\Http\Request;

class PurchaseOrderExportResource extends ExportResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  Request  $request
     * @return array
     */
    public function toArray($request): array
    {
        $resource = [];

        if (isset($this->purchaseOrderItems)) {
            foreach ($this->purchaseOrderItems as $purchaseOrderItem) {
                $resource[] = [
                    'purchase_order_number' => $this->number,
                    'status' => $this->getStatusText(),
                    'warehouse' => $this->warehouse->contactInformation->name,
                    'supplier' => $this->supplier->contactInformation->name,
                    'sku' => $purchaseOrderItem->product->sku,
                    'quantity' => $purchaseOrderItem->quantity,
                    'quantity_sell_ahead' => $purchaseOrderItem->quantity_sell_ahead,
                    'ordered_at' => isset($this->ordered_at) ? $this->ordered_at->format('Y-m-d H:i:s') : null,
                    'expected_at' => isset($this->expected_at) ? $this->expected_at->format('Y-m-d H:i:s') : null,
                    'tracking_number' => $this->tracking_number,
                    'tracking_url' => $this->tracking_url
                ];
            }
        }

        return $resource;
    }

    public static function columns(): array
    {
        return [
            'purchase_order_number',
            'status',
            'warehouse',
            'supplier',
            'sku',
            'quantity',
            'quantity_sell_ahead',
            'ordered_at',
            'expected_at',
            'tracking_number',
            'tracking_url'
        ];
    }
}
