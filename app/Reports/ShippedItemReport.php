<?php

namespace App\Reports;

use App\Http\Resources\ExportResources\ShippedItemReportExportResource;
use App\Http\Resources\ShippedItemReportTableResource;
use App\Models\ShipmentItem;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\Request;
use Illuminate\Support\Arr;
use Illuminate\Support\Carbon;

class ShippedItemReport extends Report
{
    protected $reportId = 'shipped_item';
    protected $dataTableResourceClass = ShippedItemReportTableResource::class;
    protected $exportResourceClass = ShippedItemReportExportResource::class;

    protected function reportTitle()
    {
        return __('Shipped Items Report');
    }

    protected function getQuery(Request $request): Builder
    {
        $tableColumns = $request->get('columns');
        $columnOrder = $request->get('order');
        $sortColumnName = 'shipment_items.created_at';
        $sortDirection = 'desc';
        $filterInputs =  $request->get('filter_form');

        $search = $request->get('search');

        if (!empty($columnOrder)) {
            $sortColumnName = $tableColumns[$columnOrder[0]['column']]['name'];
            $sortDirection = $columnOrder[0]['dir'];
        }

        if ($term = Arr::get($search, 'value')) {
            $filterInputs = [];
        }

        $customerIds = app('user')->getSelectedCustomers()->pluck('id')->toArray();

        $filterCustomerId = Arr::get($filterInputs, 'customer_id');

        if ($filterCustomerId && $filterCustomerId != 'all') {
            $customerIds = array_intersect($customerIds, [$filterCustomerId]);
        }

        $query = ShipmentItem::query()
            ->leftJoin('shipments', 'shipment_items.shipment_id', '=', 'shipments.id')
            ->leftJoin('shipping_methods', 'shipments.shipping_method_id', '=', 'shipping_methods.id')
            ->leftJoin('shipping_carriers', 'shipping_methods.shipping_carrier_id', '=', 'shipping_carriers.id')
            ->leftJoin('orders', 'shipments.order_id', '=', 'orders.id')
            ->leftJoin('order_channels', 'orders.order_channel_id', '=', 'order_channels.id')
            ->leftJoin('order_items', 'shipment_items.order_item_id', '=', 'order_items.id')
            ->leftJoin('products', 'order_items.product_id', '=', 'products.id')
            ->whereHas('shipment.order', static function (Builder $query) use ($customerIds) {
                $query->whereIn('customer_id', $customerIds);
            })
            ->when(!empty($filterInputs), static function (Builder $query) use ($filterInputs) {
                if (Arr::get($filterInputs, 'start_date') || Arr::get($filterInputs, 'end_date')) {
                    $startDate = Carbon::parse($filterInputs['start_date'] ?? '1970-01-01')->startOfDay();
                    $endDate = Carbon::parse($filterInputs['end_date'] ?? Carbon::now())->endOfDay();

                    $query->whereBetween('shipments.created_at', [$startDate, $endDate]);
                }
            })
            ->when(!empty($term), static function (Builder $query) use ($term) {
                $term = $term . '%';

                $query->where('order.number', 'like', $term);
            })
            ->select('shipment_items.*')
            ->groupBy('shipment_items.id')
            ->orderBy($sortColumnName, $sortDirection);

        return $query;
    }
}
