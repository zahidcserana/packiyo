<?php

namespace App\Http\Resources\ExportResources;

use Illuminate\Http\Resources\Json\JsonResource;

abstract class ExportResource extends JsonResource
{
    public static function columns(): array
    {
        return [];
    }
}
