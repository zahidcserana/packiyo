<?php

namespace App\Http\Resources\ExportResources;

use Illuminate\Http\Request;

class PickingBatchReportExportResource extends ExportResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  Request  $request
     * @return array
     */
    public function toArray($request)
    {
        $orderIds = [];
        
        foreach ($this->pickingBatchItems as $pickingBatchItem) {
            $orderIds[] = $pickingBatchItem->orderItem->order->id;
        }
        
        return [
            'id' => $this->id,
            'start_date_time' => user_date_time($this->created_at, true),
            'last_action_date_time' => $this->pickingBatchItems()->latest('updated_at')->first() ? user_date_time($this->pickingBatchItems()->latest('updated_at')->first()->updated_at, true) : '',
            'total_products_in_batch' => $this->pickingBatchItems->sum('quantity'),
            'total_picked_products' => $this->pickingBatchItems->sum('quantity_picked'),
            'amount_of_orders' => count(array_count_values($orderIds)),
            'user' => $this->pickingBatchItems->first()?->toteOrderItems->first()?->user?->contactInformation?->name
        ];
    }

    public static function columns(): array
    {
        return [
            'ID',
            'Start date/time',
            'Last action date/time',
            'Total products in batch',
            'Total picked products',
            'Amount of orders',
            'User'
        ];
    }
}
