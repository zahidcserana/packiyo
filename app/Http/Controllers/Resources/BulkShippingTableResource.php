<?php

namespace App\Http\Controllers\Resources;

use App\Models\Shipment;
use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Support\Facades\Storage;

class BulkShippingTableResource extends JsonResource
{
    public function toArray($request)
    {
        unset($resource);

        $orderItems = [];
        $firstOrder = $this->orders->first();
        if ($firstOrder && $firstOrder->orderItems->count() > 0) {
            $orderItems = $firstOrder
                ->orderItems
                ->map(function($orderItem) {
                    return [
                        'image' => $orderItem->product->productImages->first()->source ?? asset('img/no-image.png'),
                        'quantity' => $orderItem->quantity,
                        'sku' => $orderItem->sku,
                        'name' => $orderItem->name,
                    ];
                })->toArray();
        }

        $resource['id'] = $this->id;
        $resource['created_at'] = user_date_time($this->created_at, true);
        $resource['updated_at'] = user_date_time($this->updated_at, true);
        $resource['shipped_at'] = user_date_time($this->shipped_at, true);
        $resource['order_items'] = $orderItems;
        $resource['batch_key'] = $this->batch_key;
        $resource['total_orders'] = $this->total_orders;
        $resource['total_items'] = $this->total_items * $this->total_orders;
        $resource['label_pdf'] = Storage::url($this->label);
        $resource['bulk_ship_shipping_page_url'] = route('bulk_shipping.shipping', $this);
        $resource['mark_as_printed_url'] = route('bulk_shipping.markAsPrinted', $this);
        $resource['mark_as_packed_url'] = route('bulk_shipping.markAsPacked', $this);
        $resource['labels'] = [
            [
                'url' => Storage::url($this->label),
                'name' => 'Merged label'
            ]
        ];

        foreach ($this->orders as $order) {
            if (!$order->pivot->labels_merged) {
                $shipment = Shipment::find($order->pivot->shipment_id);

                if ($shipment) {
                    foreach ($shipment->shipmentLabels ?? [] as $shipmentLabel) {
                        $resource['labels'][] = [
                            'url' => route('shipment.label', [
                                'shipment' => $shipment,
                                'shipmentLabel' => $shipmentLabel
                            ]),
                            'name' => 'Failed to merged for order ' . $order->number
                        ];
                    }
                }
            }
        }

        if ($this->printedUser) {
            $resource['printed_by'] = $this->printedUser->contactInformation->name . ' | ' . $this->printed_at;
        }

        if ($this->packedUser) {
            $resource['packed_by'] = $this->packedUser->contactInformation->name . ' | ' . $this->packed_at;
        }

        return $resource;
    }
}
