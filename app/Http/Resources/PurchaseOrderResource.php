<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class PurchaseOrderResource extends JsonResource
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

        $resource['warehouse'] = new WarehouseResource($this->warehouse);
        unset($resource['warehouse_id']);

        $resource['supplier'] = new SupplierResource($this->supplier);
        unset($resource['supplier_id']);

        $resource['purchase_order_items'] = new PurchaseOrderItemCollection($this->purchaseOrderItems);

        return $resource;
    }
}
