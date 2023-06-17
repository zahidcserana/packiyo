<?php

namespace App\Reports;

use App\Http\Resources\ExportResources\ReplenishmentReportExportResource;
use App\Http\Resources\ReplenishmentReportTableResource;
use App\Models\Product;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\Request;
use Illuminate\Support\Arr;
use Illuminate\Support\Carbon;

class ReplenishmentReport extends Report
{
    protected $reportId = 'replenishment';
    protected $dataTableResourceClass = ReplenishmentReportTableResource::class;
    protected $exportResourceClass = ReplenishmentReportExportResource::class;

    protected function reportTitle()
    {
        return __('Replenishment Report');
    }

    protected function getQuery(Request $request): ?Builder
    {
        $tableColumns = $request->get('columns');
        $columnOrder = $request->get('order');
        $sortColumnName = 'products.id';
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

        return Product::query()->whereIn('customer_id', $customerIds)
            ->whereRaw('quantity_to_replenish > 0')
            ->when(!empty($filterInputs), static function (Builder $query) use ($filterInputs) {
                if (Arr::get($filterInputs, 'start_date') || Arr::get($filterInputs, 'end_date')) {
                    $startDate = Carbon::parse($filterInputs['start_date'] ?? '1970-01-01')->startOfDay();
                    $endDate = Carbon::parse($filterInputs['end_date'] ?? Carbon::now())->endOfDay();

                    $query->whereBetween('products.created_at', [$startDate, $endDate]);
                }
            })
            ->when(!empty($term), static function (Builder $query) use ($term) {
                $term = $term . '%';

                $query->where(static function (Builder $query) use ($term) {
                    $query->where('products.name', 'like', $term)
                        ->orWhere('products.sku', 'like', $term);
                });
            })
            ->orderBy($sortColumnName, $sortDirection);
    }
}
