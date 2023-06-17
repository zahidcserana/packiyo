<?php

namespace App\Http\Resources;

use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class StaleInventoryReportTableResource extends JsonResource
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

        $lastSold = $this->created_at->diffInDays(Carbon::now());

        $resource['product_name'] = $this->product->name;
        $resource['product_url'] = route('product.edit', ['product' => $this->product]);
        $resource['sku'] = $this->product->sku;
        $resource['quantity_on_hand'] = $this->product->quantity_on_hand;
        $resource['last_sold_at'] = user_date_time($this->created_at);
        $resource['last_sold'] = trans_choice('{0} Today|{1} Yesterday|{2,*} :days days ago', $lastSold, ['days' => $lastSold]);
        $resource['amount_sold'] = $this->amount_sold ?? 0;
        $resource['sold_in_last_30_days'] = $this->sold_in_last_30_days ?? 0;
        $resource['sold_in_last_60_days'] = $this->sold_in_last_60_days ?? 0;
        $resource['sold_in_last_180_days'] = $this->sold_in_last_180_days ?? 0;
        $resource['sold_in_last_365_days'] = $this->sold_in_last_365_days ?? 0;

        return $resource;
    }
}
