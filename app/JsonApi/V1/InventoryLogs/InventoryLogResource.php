<?php

namespace App\JsonApi\V1\InventoryLogs;

use App\Http\Resources\LocationResource;
use App\Http\Resources\PurchaseOrderResource;
use App\Http\Resources\ReturnResource;
use App\Http\Resources\ShipmentResource;
use App\Models\Location;
use App\Models\PurchaseOrder;
use App\Models\Return_;
use App\Models\Shipment;
use Illuminate\Http\Request;
use LaravelJsonApi\Core\Resources\JsonApiResource;

class InventoryLogResource extends JsonApiResource
{

    /**
     * Get the resource's attributes.
     *
     * @param Request|null $request
     * @return iterable
     */
    public function attributes($request): iterable
    {
        $this->user->user_role = $this->user->role;

        $customer_user_role = [];
        foreach ($this->user->customers as $customer) {
           $customer_user_role[] = $customer->pivot->load('role');
        }

        $resource = [];

        $resource['source'] = match ($this->source_type) {
            Location::class => new LocationResource($this->source),
            PurchaseOrder::class => new PurchaseOrderResource($this->source),
            Return_::class => new ReturnResource($this->source),
        };

        $resource['destination'] = match ($this->destination_type) {
            Location::class => new LocationResource($this->destination),
            Shipment::class => new ShipmentResource($this->destination),
        };

        unset($this->user->role);
        unset($this->user->customers);

        $this->user->customer_user_role = $customer_user_role;

        return [
            'id' => $this->id,
            'source_type' => $this->source_type,
            'destination_type' => $this->destination_type,
            'quantity' => $this->quantity,
            'createdAt' => $this->created_at,
            'updatedAt' => $this->updated_at,
            'deletedAt' => $this->deleted_at,
            'user' => $this->user->load('contactInformation'),
            'product' => $this->product
                ->load(['customer'=> function($sub) {
                    return $sub->with(['contactInformation', 'parent'=> function($query) {
                        return $query->with('contactInformation');
                    }]);
            }]),
            'source' => $resource['source'] ?? null,
            'destination' => $resource['destination'] ?? null,
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
            // @TODO
        ];
    }

}
