<?php

namespace App\Http\Resources\ExportResources;

use Illuminate\Http\Request;

class ProductExportResource extends ExportResource
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
            'sku' => $this->sku,
            'name' => $this->name,
            'barcode' => $this->barcode,
            'price' => $this->price,
            'value' => $this->value,
            'hs_code' => $this->hs_code,
            'weight' => $this->weight,
            'height' => $this->height,
            'length' => $this->length,
            'width' => $this->width,
            'quantity_on_hand' => $this->quantity_on_hand,
            'quantity_available' => $this->quantity_available,
            'quantity_allocated' => $this->quantity_allocated,
            'quantity_backordered' => $this->quantity_backordered,
            'notes' => $this->notes,
            'image' => $this->productImages->first()->source ?? '',
            'vendor' => $this->suppliers->pluck('contactInformation.name')->join(';')
        ];
    }

    public static function columns(): array
    {
        return [
            'sku',
            'name',
            'barcode',
            'price',
            'value',
            'hs_code',
            'weight',
            'height',
            'length',
            'width',
            'quantity_on_hand',
            'quantity_available',
            'quantity_allocated',
            'quantity_backordered',
            'notes',
            'image',
            'vendor'
        ];
    }
}
