<?php

namespace App\Reports;

use App\Http\Resources\{ExportResources\StaleInventoryReportExportResource, StaleInventoryReportTableResource};
use App\Models\{InventoryLog, Shipment};
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\{Request};
use Illuminate\Support\{Arr, Facades\DB};

class StaleInventoryReport extends Report
{
    protected $reportId = 'stale_inventory';
    protected $dataTableResourceClass = StaleInventoryReportTableResource::class;
    protected $exportResourceClass = StaleInventoryReportExportResource::class;

    protected function reportTitle()
    {
        return __('Stale Inventory Report');
    }

    protected function getQuery(Request $request): ?Builder
    {
        $tableColumns = $request->get('columns');
        $columnOrder = $request->get('order');
        $sortColumnName = 'products.id';
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

        $latestSoldProducts = DB::table('inventory_logs')
            ->where('inventory_logs.associated_object_type', Shipment::class)
            ->select('product_id', DB::raw('MAX(created_at) as last_sold_at'), DB::raw('ABS(SUM(quantity)) as amount_sold'))
            ->groupBy('product_id');

        $query = InventoryLog::with(['product', 'product.customer', 'product.customer.contactInformation'])
            ->join('users', 'inventory_logs.user_id', '=', 'users.id')
            ->join('products', 'inventory_logs.product_id', '=', 'products.id')
            ->whereIn('products.customer_id', $customerIds)
            ->joinSub($latestSoldProducts, 'latest_sold_products', function ($join) {
                $join->on('inventory_logs.created_at', '=', 'latest_sold_products.last_sold_at');
                $join->on('inventory_logs.product_id', '=', 'latest_sold_products.product_id');
            })
            ->leftJoinSub($this->soldItemsForPeriod(30), 'sold_last_30_days', function ($join) {
                $join->on('inventory_logs.product_id', '=', 'sold_last_30_days.product_id');
            })
            ->leftJoinSub($this->soldItemsForPeriod(60), 'sold_last_60_days', function ($join) {
                $join->on('inventory_logs.product_id', '=', 'sold_last_60_days.product_id');
            })
            ->leftJoinSub($this->soldItemsForPeriod(180), 'sold_last_180_days', function ($join) {
                $join->on('inventory_logs.product_id', '=', 'sold_last_180_days.product_id');
            })
            ->leftJoinSub($this->soldItemsForPeriod(365), 'sold_last_365_days', function ($join) {
                $join->on('inventory_logs.product_id', '=', 'sold_last_365_days.product_id');
            })
            ->when(!empty($filterInputs), static function (Builder $query) use ($filterInputs) {
                if (Arr::get($filterInputs, 'start_date') || Arr::get($filterInputs, 'end_date')) {
                    $startDate = \Illuminate\Support\Carbon::parse($filterInputs['start_date'] ?? '1970-01-01')->startOfDay();
                    $endDate = Carbon::parse($filterInputs['end_date'] ?? Carbon::now())->endOfDay();

                    $query->whereBetween('inventory_logs.created_at', [$startDate, $endDate]);
                }

                if (Arr::get($filterInputs, 'has_not_sold_in') && Arr::get($filterInputs, 'has_not_sold_in') !== '0') {
                    $hasNotSoldIn = (int) Arr::get($filterInputs, 'has_not_sold_in');

                    $query->where('inventory_logs.created_at', '<', Carbon::now()->subDays($hasNotSoldIn));
                }
            })
            ->when(!empty($term), static function (Builder $query) use ($term) {
                $term = $term . '%';

                $query->where(static function (Builder $query) use ($term) {
                    $query->where('products.name', 'like', $term)
                        ->orWhere('products.sku', 'like', $term);
                });
            })
            ->select('inventory_logs.*',
                'latest_sold_products.amount_sold',
                'latest_sold_products.last_sold_at',
                'sold_last_30_days.amount_sold as sold_in_last_30_days',
                'sold_last_60_days.amount_sold as sold_in_last_60_days',
                'sold_last_180_days.amount_sold as sold_in_last_180_days',
                'sold_last_365_days.amount_sold as sold_in_last_365_days'
            )
            ->groupBy('inventory_logs.product_id')
            ->orderBy($sortColumnName, $sortDirection);

        return $query;
    }

    private function soldItemsForPeriod(int $days): \Illuminate\Database\Query\Builder
    {
        return DB::table('inventory_logs')
            ->where('inventory_logs.associated_object_type', Shipment::class)
            ->where('inventory_logs.created_at', '>=', Carbon::now()->subDays($days))
            ->select('product_id', DB::raw('ABS(SUM(quantity)) as amount_sold'))
            ->groupBy('product_id');
    }
}
