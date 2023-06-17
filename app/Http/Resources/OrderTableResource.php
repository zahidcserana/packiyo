<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class OrderTableResource extends JsonResource
{
    /**
     * Transform the resource collection into an array.
     *
     * @param  Request  $request
     * @return array
     */
    public function toArray($request): array
    {
        unset($resource);

        $resource['id'] = $this->id;
        $resource['number'] = $this->number;
        $resource['customer'] = ['url' =>route('customer.edit', ['customer' => $this->customer]), 'name' => $this->customer->contactInformation->name];
        $resource['order_channel_name'] = $this->orderChannel->name ?? '';
        $resource['order_slip_url'] = route('order.getOrderSlip', $this);
        $resource['order_status_name'] = $this->getStatusText();
        $resource['shipping_name'] = $this->shippingContactInformation->name;
        $resource['shipping_address'] = $this->shippingContactInformation->address;
        $resource['shipping_city'] = $this->shippingContactInformation->city;
        $resource['shipping_state'] = $this->shippingContactInformation->state;
        $resource['shipping_zip'] = $this->shippingContactInformation->zip;
        $resource['shipping_country'] = $this->shippingContactInformation->country->name ?? '';
        $resource['shipping_email'] = $this->shippingContactInformation->email;
        $resource['shipping_phone'] = $this->shippingContactInformation->phone;
        $resource['priority'] = $this->priority;
        $resource['priority_score'] = $this->priority_score;
        $resource['link_edit'] = route('order.edit', ['order' => $this->id ]);
        $resource['link_delete'] = ['token' => csrf_token(), 'url' => route('order.destroy', ['id' => $this->id, 'order' => $this])];
        $resource['ready_to_ship'] = $this->ready_to_ship ? 'YES' : 'NO';
        $resource['ready_to_pick'] = $this->ready_to_pick ? 'YES' : 'NO';
        $resource['allow_partial'] = $this->allow_partial ? 'YES' : 'NO';
        $resource['tote'] = $this->getTote();
        $resource['ordered_at'] = user_date_time($this->ordered_at, true);
        $resource['order_status_color'] = $this->orderStatus->color ?? null;
        $resource['tags'] = $this->tags->pluck('name')->join(', ');
        $resource['required_shipping_date_at'] = $this->required_shipping_date_at ? user_date_time($this->required_shipping_date_at) : '';
        $resource['shipping_date_before_at'] = $this->shipping_date_before_at ? user_date_time($this->shipping_date_before_at) : '';

        return $resource;
    }

    private function getTote(): ?array
    {
        foreach ($this->orderItems as $orderItem) {
            if (!empty($orderItem->tote())) {
                return ['url' => route('tote.edit', ['tote' => $orderItem->tote()]), 'name' => empty($orderItem->tote()->name) ? 'Unknown' : $orderItem->tote()->name];
            }
        }

        return null;
    }
}
