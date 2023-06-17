<?php

namespace App\Http\Resources\ExportResources;

use Illuminate\Http\Request;

class InventoryExportResource extends ExportResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  Request  $request
     * @return array
     */
    public function toArray($request): array
    {
        return [
            'sku' => $this->product->sku,
            'location' => $this->location->name,
            'quantity' => $this->quantity_on_hand
        ];
    }

    public static function columns(): array
    {
        return [
            'sku',
            'location',
            'quantity'
        ];
    }
}
