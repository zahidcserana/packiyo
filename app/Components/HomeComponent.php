<?php

namespace App\Components;

use App\Models\Customer;
use App\Models\Order;
use App\Models\PickingBatch;
use App\Models\Shipment;
use App\Models\Task;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\Request;
use Webpatser\Countries\Countries;

class HomeComponent extends BaseComponent
{
    public function statistics(Request $request) {
        $customerIds = Auth()->user()->customerIds(true, true);

        if (in_array($request->customer_id, $customerIds)) {
            $customerIds = Customer::where('id', $request->customer_id)->orWhere('parent_id', $request->customer_id)->pluck('id')->toArray();
        } else {
            $customerIds = [];
        }

        return [
            'ordersReadyToShip' => $this->ordersReadyToShip($customerIds),
            'itemsReadyToShip' => $this->itemsReadyToPick($customerIds),
            'ordersDueToday' => $this->ordersDueToday($customerIds),
            'itemsShippedToday' => $this->ordersShippedToday($customerIds),
            'batchesLeft' => $this->batchesLeft($customerIds),
        ];
    }

    public function ordersReadyToShip($customerIds)
    {
        $orders = Order::whereIntegerInRaw('customer_id', $customerIds)->where('ready_to_ship', '1');

        return $orders->count();
    }

    public function itemsReadyToPick($customerIds)
    {
        $orders = Order::whereIntegerInRaw('customer_id', $customerIds)
            ->where('ready_to_pick', '1')
            ->withSum('orderItems', 'quantity')
            ->get();

        return $orders->sum('order_items_sum_quantity');
    }

    public function ordersDueToday($customerIds)
    {
        $orders = Order::whereIntegerInRaw('customer_id', $customerIds)
            ->whereNull('fulfilled_at')
            ->whereNull('cancelled_at')
            ->whereNotNull('shipping_date_before_at');

        return $orders->count();
    }

    public function ordersShippedToday($customerIds)
    {
        $orders = Order::whereIntegerInRaw('customer_id', $customerIds)
            ->whereDate('fulfilled_at', Carbon::today());

        return $orders->count();
    }

    public function batchesLeft($customerIds)
    {
        $user = auth()->user();

        $data = [
            'sib' => 0,
            'mib' => 0,
            'so' => 0,
        ];

        $taskIds = Task::where('user_id', $user->id)->whereIntegerInRaw('customer_id', $customerIds)->where('taskable_type', PickingBatch::class)->where('completed_at', null)->pluck('taskable_id')->toArray();;
        $pickingBatches = PickingBatch::whereIntegerInRaw('id', $taskIds)->get();

        foreach ($pickingBatches as $pickingBatch) {
            $data[$pickingBatch->type]++;
        }

        return $data;
    }

    public function getCountries()
    {
        return Countries::pluck('name', 'id')->all();
    }
}
