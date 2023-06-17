<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class PackingSingleOrderShippingTableResource extends JsonResource
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

        $itemsImages = [];
        $totes = [];
        $itemsCount = 0;
        $orderProducts = [];

        foreach ($this->orderItems as $orderItem){
            if ($orderItem->quantity_allocated > 0) {
                if (!$this->orderItems->contains('order_item_kit_id', $orderItem->id)) {
                    $itemsCount += $orderItem->quantity_allocated;
                }

                if (!empty($orderItem->product->productImages)) {
                    $foundImage = false;
                    foreach ($orderItem->product->productImages as $productImage) {
                        if ($productImage->source){
                            $itemsImages[] = $productImage->source;
                            $foundImage = true;
                            break;
                        }
                    }

                    if (!$foundImage){
                        $itemsImages[] = asset('img/no-image.png');
                    }
                }

                foreach ($orderItem->placedToteOrderItems as $placedToteOrderItem) {
                    if (!empty($placedToteOrderItem->tote)) {
                        $totes[$placedToteOrderItem->tote->name] = $placedToteOrderItem->tote->name;
                    }
                }
            }

            if (isset($orderProducts[$orderItem->product->id])){
                $orderProducts[$orderItem->product->id]['quantity'] += $orderItem['quantity'];
            } else{
                $orderProducts[$orderItem->product->id]['quantity'] = $orderItem['quantity'];
                $orderProducts[$orderItem->product->id]['name'] = $orderItem->product->name;
                $orderProducts[$orderItem->product->id]['sku'] = $orderItem->product->sku;
            }
        }

        $resource['id'] = $this->id;
        $resource['customer'] = ['name' => $this->customer->contactInformation->name, 'url' => route('customer.edit', ['customer' => $this->customer])];
        $resource['number'] = $this->number;
        $resource['shipping_date_before_at'] = $this->shipping_date_before_at ? user_date_time($this->shipping_date_before_at) : __('Not set');
        $resource['tote'] = implode(', ', $totes);
        $resource['items_count'] = $itemsCount;
        $resource['items_images'] = $itemsImages;
        $resource['link_packing'] = route('packing.single_order_shipping', ['order' => $this->id]);
        $resource['link_order'] = route('order.edit', ['order' => $this->id]);
        $resource['order_products'] = $orderProducts;
        $resource['country'] = $this->shippingContactInformation->country->name ?? '';

        return $resource;
    }
}
