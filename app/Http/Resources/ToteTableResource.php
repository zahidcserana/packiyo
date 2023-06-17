<?php

namespace App\Http\Resources;

use DB;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ToteTableResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  Request  $request
     * @return array
     */
    public function toArray($request): array
    {
        unset($resource);

        $resource['id'] = $this->id;
        $resource['name'] = $this->name;
        $resource['barcode'] = $this->barcode;
        $resource['warehouse'] = $this->warehouse->contactInformation->name;
        $resource['created_at'] = user_date_time($this->created_at);
        $resource['tote_items'] = $this->placedToteOrderItems()->sum(DB::raw('quantity_remaining'));
        $resource['link_edit'] = route('tote.edit', ['tote' => $this ]);
        $resource['link_delete'] = ['token' => csrf_token(), 'url' => route('tote.destroy', ['tote' => $this, 'id' => $this->id])];
        $resource['link_clear_tote'] = ['token' => csrf_token(), 'url' => route('tote.clear', ['tote' => $this, 'id' => $this->id])];
        $resource['link_print_barcode'] = route('tote.barcode', ['tote' => $this ]);

        return $resource;
    }
}
