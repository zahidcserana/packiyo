<?php

namespace App\Http\Resources\ExportResources;

use Illuminate\Http\Request;

class PickerReportExportResource extends ExportResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  Request  $request
     * @return array
     */
    public function toArray($request)
    {
        return [
            'name' => $this->user->contactInformation->name,
            'items_count' => $this->items_count,
            'unique_items_count' => $this->unique_items_count,
            'orders_count' => $this->orders_count,
        ];
    }

    public static function columns(): array
    {
        return [
            'name',
            'items_count',
            'unique_items_count',
            'orders_count',
        ];
    }
}
