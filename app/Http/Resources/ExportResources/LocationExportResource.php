<?php

namespace App\Http\Resources\ExportResources;

use App\Http\Resources\Request;

class LocationExportResource extends ExportResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  Request  $request
     * @return array
     */
    public function toArray($request): array
    {
        $resource = [];


        $resource = [
            'warehouse' => $this->warehouse->contactInformation->name,
            'name'      => $this->name,
            'barcode'   => $this->barcode,
            'pickable'  => $this->pickable ? 'YES' : 'NO',
            'sellable'  => $this->sellable ? 'YES' : 'NO',
        ];

        return $resource;
    }

    public static function columns(): array
    {
        return [
            'warehouse',
            'name',
            'barcode',
            'pickable',
            'sellable',
        ];
    }
}
