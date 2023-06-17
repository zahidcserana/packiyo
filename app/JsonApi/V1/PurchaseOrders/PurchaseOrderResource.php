<?php

namespace App\JsonApi\V1\PurchaseOrders;

use Illuminate\Http\Request;
use LaravelJsonApi\Core\Resources\JsonApiResource;

class PurchaseOrderResource extends JsonApiResource
{

    /**
     * Get the resource's attributes.
     *
     * @param Request|null $request
     * @return iterable
     */
    public function attributes($request): iterable
    {
        $this->purchaseOrderItems = $this->purchaseOrderItems->load(['product']);

        return [
            'id' => $this->id,
            'external_id' => $this->external_id,
            'purchase_order_status_id' => $this->purchase_order_status_id,
            'number' => $this->number,
            'ordered_at' => $this->ordered_at,
            'expected_at' => $this->expected_at,
            'delivered_at' => $this->delivered_at,
            'notes' => $this->notes,
            'priority' => $this->priority,
            'createdAt' => $this->created_at,
            'updatedAt' => $this->updated_at,
            'deletedAt' => $this->updated_at,
            'customer' => $this->customer->load(['contactInformation', 'parent' => function($query) {
                return $query->with('contactInformation');
            }]),
            'warehouse' => $this->warehouse
                ->load(['contactInformation', 'customer' => function($query) {
                $query->with('contactInformation');
            }]),
            'supplier' => $this->supplier
                ->load(['contactInformation', 'customer' => function($query) {
                $query->with('contactInformation');
            }]),
            'purchase_order_items' => $this->purchaseOrderItems,
            'rejected_items' => $this->rejectedItems
        ];
    }

    /**
     * Get the resource's relationships.
     *
     * @param Request|null $request
     * @return iterable
     */
    public function relationships($request): iterable
    {
        return [
            $this->relation('customer')->showDataIfLoaded()->withoutLinks(),
            $this->relation('supplier')->showDataIfLoaded()->withoutLinks(),
            $this->relation('warehouses')->showDataIfLoaded()->withoutLinks(),
            $this->relation('purchase_order_items')->showDataIfLoaded()->withoutLinks()
        ];
    }

}
