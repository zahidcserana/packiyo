<?php

namespace App\Reports;

use App\Http\Resources\ExportResources\PackerReportExportResource;
use App\Http\Resources\PackerReportTableResource;
use App\Models\Shipment;
use App\Models\User;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\Request;
use Illuminate\Support\Arr;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;

class PackerReport extends Report
{
    protected $reportId = 'packer';
    protected $dataTableResourceClass = PackerReportTableResource::class;
    protected $exportResourceClass = PackerReportExportResource::class;

    protected function reportTitle()
    {
        return __('Packers Report');
    }

    protected function getQuery(Request $request): ?Builder
    {
        $customerIds = app('user')->getSelectedCustomers()->pluck('id')->toArray();

        $tableColumns = $request->get('columns');
        $columnOrder = $request->get('order');
        $sortColumnName = 'contact_informations.name';
        $sortDirection = 'asc';
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

        $query = Shipment::with(['user.contactInformation'])
            ->leftJoin('shipment_items', 'shipments.id', '=', 'shipment_items.shipment_id')
            ->leftJoin('orders', 'shipments.order_id', '=', 'orders.id')
            ->leftJoin('contact_informations', 'shipments.user_id', '=', 'contact_informations.object_id')
            ->where('contact_informations.object_type', User::class)
            ->whereIn('orders.customer_id', $customerIds)
            ->when(!empty($filterInputs), static function (Builder $query) use ($filterInputs) {
                if (Arr::get($filterInputs, 'start_date') || Arr::get($filterInputs, 'end_date')) {
                    $startDate = Carbon::parse($filterInputs['start_date'] ?? '1970-01-01')->startOfDay();
                    $endDate = Carbon::parse($filterInputs['end_date'] ?? Carbon::now())->endOfDay();

                    $query->whereBetween('shipments.updated_at', [$startDate, $endDate]);
                }

                $bulkShipped = Arr::get($filterInputs, 'bulk_shipped');
                if (!is_null($bulkShipped)) {
                    if ($bulkShipped == 1) {
                        $query->has('bulkShipBatch');
                    } else {
                        $query->doesntHave('bulkShipBatch');
                    }
                }
            })
            ->when(!empty($term), static function (Builder $query) use ($term) {
                $term = $term . '%';

                $query->whereHas('user.contactInformation', static function (Builder $query) use ($term) {
                    $query->where('name', 'like', $term);
                });
            })
            ->select('shipments.*')
            ->addSelect(DB::raw('COUNT(DISTINCT shipments.id) as shipments_count'))
            ->addSelect(DB::raw('SUM(shipment_items.quantity) as items_count'))
            ->addSelect(DB::raw('COUNT(shipment_items.id) as unique_items_count'))
            ->addSelect(DB::raw('COUNT(DISTINCT orders.id) as orders_count'))
            ->groupBy('shipments.user_id')
            ->orderBy($sortColumnName, $sortDirection);

        return $query;
    }
}
