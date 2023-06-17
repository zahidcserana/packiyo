<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ReturnTableResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  Request  $request
     * @return array
     */
    public function toArray($request)
    {
        $orderProducts = [];

        $trackingNumbers = '';
        $returnLabels = '';

        foreach($this->returnTrackings as $tracking) {
            $trackingNumbers .= '<a href="' . $tracking->tracking_url . '" target="_blank" class="text-neutral-text-gray">' . $tracking->tracking_number . '</a><br/>';
        }

        foreach ($this->returnLabels as $key => $returnLabel) {
            $route = route('return.label', [
                'return' => $this,
                'returnLabel' => $returnLabel,
            ]);

            $label = __('Label :number', ['number' => $key + 1]);

            $returnLabels .= '<a href="' . $route .'" title="' . $label . '" target="_blank" class="text-neutral-text-gray"><i class="picon-tag-light icon-lg"></i></a><br/> ';
        }

        foreach( $this->order->orderItems as $orderItem ){
            if (isset($orderProducts[$orderItem->product->id])){
                $orderProducts[$orderItem->product->id]['quantity'] += $orderItem['quantity'];
            }
            else{
                $orderProducts[$orderItem->product->id]['quantity'] = $orderItem['quantity'];
                $orderProducts[$orderItem->product->id]['name'] = $orderItem->product->name;
                $orderProducts[$orderItem->product->id]['sku'] = $orderItem->product->sku;
            }
        }

        return [
            'id' => $this->id,
            'number' => $this->number,
            'returnStatus' => $this->getStatusText(),
            'returnStatusColor' => $this->returnStatus->color ?? null,
            'order' => [
                'number' => $this->order->number,
                'url' => route('order.edit', ['order' => $this->order]),
                'created_at' => user_date_time($this->order->created_at),
            ],
            'order_products' => $orderProducts,
            'returnItems' => $this->items->map(function($item) {
                return implode(' ', [
                     $item->product->sku,
                    $item->product->name,
                    $item->quantity,
                ]);
            })->toArray(),
            'reason' => $this->reason,
            'created_at' => user_date_time($this->created_at),
            'city' => $this->order->shippingContactInformation->city,
            'zip' => $this->order->shippingContactInformation->zip,
            'tracking_number' => $trackingNumbers,
            'return_labels' => $returnLabels,
            'link_edit' => route('return.edit', ['return' => $this]),
            'link_delete' => [
                'token' => csrf_token(),
                'url' => route('return.destroy', ['id' => $this->id, 'return' => $this])
            ]
        ];
    }
}
