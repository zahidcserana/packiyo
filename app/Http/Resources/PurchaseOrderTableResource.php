<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class PurchaseOrderTableResource extends JsonResource
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

        $resource['id'] = $this->id;
        $resource['number'] = $this->number;
        $resource['customer'] = isset($this->customer->contactInformation) ? ['name' => $this->customer->contactInformation->name, 'url' => route('customer.edit', ['customer' => $this->customer])] : '';
        $resource['supplier'] = isset($this->supplier->contactInformation) ? ['name' => $this->supplier->contactInformation->name, 'url' => route('supplier.edit', ['supplier' => $this->supplier])] : '';
        $resource['warehouse'] = isset($this->warehouse->contactInformation) ? ['name' => $this->warehouse->contactInformation->name, 'url' => route('warehouses.edit', ['warehouse' => $this->warehouse])] : '';
        $resource['status'] = $this->getStatusText();
        $resource['ordered_at'] = $this->ordered_at ? user_date_time($this->ordered_at) : user_date_time($this->created_at);
        $resource['expected_at'] = $this->expected_at ? user_date_time($this->expected_at) : null;
        $resource['delivered_at'] = $this->delivered_at ? user_date_time($this->delivered_at) : null;
        $resource['received_at'] = $this->received_at ? user_date_time($this->received_at) : null;
        $resource['link_edit'] = route('purchase_orders.edit', ['purchase_order' => $this]);
        $resource['link_delete'] = ['token' => csrf_token(), 'url' => route('purchase_orders.destroy', ['id' => $this->id, 'purchase_order' => $this])];
        $resource['link_receive'] = ['img' => asset('img/rpo.png'), 'url' => route('purchase_order.receive', ['purchaseOrder' => $this])];
//        $resource['is_3pl'] = (bool) $this->customer->isParent();
        $resource['is_3pl'] = true;

        return $resource;
    }
}
