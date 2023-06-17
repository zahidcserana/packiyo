<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class OrderResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array
     */
    public function toArray($request)
    {
        $resource = parent::toArray($request);

        $resource['customer'] = new CustomerResource($this->customer);
        unset($resource['customer_id']);
        $resource['order_items'] = new OrderItemCollection($this->orderItems);

        $resource['shipments'] = new ShipmentCollection($this->shipments);

        $resource['shipping_contact_information'] = new ContactInformationResource($this->shippingContactInformation);
        unset($resource['shipping_contact_information_id']);

        $resource['billing_contact_information'] = new ContactInformationResource($this->billingContactInformation);
        unset($resource['billing_contact_information_id']);

        $resource['order_lock_information'] = new OrderLockResource($this->orderLock);

        return $resource;
    }
}
