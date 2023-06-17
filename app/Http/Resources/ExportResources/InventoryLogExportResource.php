<?php

namespace App\Http\Resources\ExportResources;

use Illuminate\Http\Request;

class InventoryLogExportResource extends ExportResource
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
            'customer' => $this->product->customer->contactInformation->name,
            'date' => $this->created_at->format('Y-m-d H:i:s'),
            'warehouse' => $this->location->warehouse->contactInformation->name,
            'sku' => $this->product->sku,
            'product' => $this->product->name,
            'location' => $this->location->name,
            'previous_on_hand' => $this->previous_on_hand,
            'new_on_hand' => $this->new_on_hand,
            'reason' => $this->getReasonText(),
            'changed_by' => $this->user->contactInformation->name
        ];
    }

    public static function columns(): array
    {
        return [
            'customer',
            'date',
            'warehouse',
            'sku',
            'product',
            'location',
            'previous_on_hand',
            'new_on_hand',
            'reason',
            'changed_by'
        ];
    }
}
