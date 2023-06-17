<?php

namespace App\Reports;

use App\Http\Resources\ExportResources\SerialNumberReportExportResource;
use App\Http\Resources\SerialNumberReportTableResource;
use App\Models\PackageOrderItem;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\Request;
use Illuminate\Support\Arr;
use Illuminate\Support\Carbon;

class SerialNumberReport extends Report
{
    protected $reportId = 'serial_number';
    protected $dataTableResourceClass = SerialNumberReportTableResource::class;
    protected $exportResourceClass = SerialNumberReportExportResource::class;

    protected function reportTitle()
    {
        return __('Serial Numbers Report');
    }

    protected function getQuery(Request $request): ?Builder
    {
        $tableColumns = $request->get('columns');
        $columnOrder = $request->get('order');
        $sortColumnName = 'package_order_items.created_at';
        $sortDirection = 'desc';
        $filterInputs = $request->get('filter_form');
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

        $query = PackageOrderItem::query()
            ->leftJoin('order_items', 'package_order_items.order_item_id', '=', 'order_items.id')
            ->leftJoin('products', 'order_items.product_id', '=', 'products.id')
            ->leftJoin('packages', 'package_order_items.package_id', '=', 'packages.id')
            ->leftJoin('shipments', 'packages.shipment_id', '=', 'shipments.id')
            ->leftJoin('orders', 'order_items.order_id', '=', 'orders.id')
            ->where('serial_number', '<>', '')
            ->where('products.has_serial_number', true)
            ->whereHas('orderItem.order', static function (Builder $query) use ($customerIds) {
                $query->whereIn('customer_id', $customerIds);
            })
            ->when(!empty($filterInputs), static function (Builder $query) use ($filterInputs) {
                if (Arr::get($filterInputs, 'start_date') || Arr::get($filterInputs, 'end_date')) {
                    $startDate = Carbon::parse($filterInputs['start_date'] ?? '1970-01-01')->startOfDay();
                    $endDate = Carbon::parse($filterInputs['end_date'] ?? Carbon::now())->endOfDay();

                    $query->whereBetween('package_order_items.created_at', [$startDate, $endDate]);
                }
            })
            ->when(!empty($term), static function(Builder $query) use ($term) {
                $term = $term . '%';

                $query->where(static function ($query) use ($term) {
                    $query->whereHas('orderItem.product', static function ($query) use ($term) {
                        $query->where('name', 'like', $term)
                            ->orWhere('sku', 'like', $term);
                    })
                    ->orWhereHas('orderItem.order', static function ($query) use ($term) {
                        $query->where('number', 'like', $term);
                    })
                    ->orWhere('serial_number', 'like', $term);
                });
            })
            ->select('package_order_items.*')
            ->groupBy('package_order_items.id')
            ->orderBy($sortColumnName, $sortDirection);

        return $query;
    }
}
