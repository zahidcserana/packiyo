<?php

namespace App\Http\Resources\ExportResources;

use Illuminate\Http\Request;

class TotesExportResource extends ExportResource
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
            'name' => $this->name,
            'barcode' => $this->barcode,
            'warehouse' => $this->warehouse->contactInformation->name,
            'delete' => 'no'
        ];
    }

    public static function columns(): array
    {
        return [
            'name',
            'barcode',
            'warehouse',
            'delete'
        ];
    }
}
