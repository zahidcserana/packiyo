<?php

namespace App\Http\Resources;

use Carbon\Carbon;
use Illuminate\Http\Resources\Json\JsonResource;

class ProductLotLocationsDateTableResource extends JsonResource
{
    public function toArray($request): array
    {
        unset($resource);

        $lot = $this->lotItems->last()->lot ?? null;

        $resource['id'] = $this->id;
        $resource['key'] = $this->key;
        $resource['name'] = $this->name;
        $resource['quantity'] = $this->pivot->quantity_on_hand ?? 0;
        $resource['lot_name'] = $lot->name ?? '';
        $resource['lot_id'] = $lot->id ?? 0;
        $resource['lot_expiration'] = $lot ? user_date_time($lot->expiration_date) : '';
        $resource['lot_vendor'] = $lot->supplier->contactInformation->name ?? '';
        $resource['location_pickable'] = $this->isPickableLabel();
        $resource['location_sellable'] = $this->isSellableLabel();

        return $resource;
    }
}
