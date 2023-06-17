<?php

namespace App\Http\Resources\ExportResources;

use Carbon\Carbon;
use Illuminate\Http\Request;

class StaleInventoryReportExportResource extends ExportResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  Request  $request
     * @return array
     */
    public function toArray($request): array
    {
        $lastSold = $this->created_at->diffInDays(Carbon::now());

        return [
            'name' => $this->product->name,
            'sku' => $this->product->sku,
            'quantity_on_hand' => $this->product->quantity_on_hand,
            'last_sold_at' => user_date_time($this->created_at),
            'last_sold' => trans_choice('{0} Today|{1} Yesterday|{2,*} :days days ago', $lastSold, ['days' => $lastSold]),
            'amount_sold_total' => $this->amount_sold ?? 0,
            'amount_sold_in_last_30_days' => $this->sold_in_last_30_days ?? 0,
            'amount_sold_in_last_60_days' => $this->sold_in_last_60_days ?? 0,
            'amount_sold_in_last_180_days' => $this->sold_in_last_180_days ?? 0,
            'amount_sold_in_last_365_days' => $this->sold_in_last_365_days ?? 0
        ];
    }

    public static function columns(): array
    {
        return [
            'name',
            'sku',
            'quantity_on_hand',
            'last_sold_at',
            'last_sold',
            'amount_sold_total',
            'amount_sold_in_last_30_days',
            'amount_sold_in_last_60_days',
            'amount_sold_in_last_180_days',
            'amount_sold_in_last_365_days'
        ];
    }
}
