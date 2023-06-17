<?php

namespace App\Reports;

use App\Http\Dto\Filters\Reports\PickingBatchReportFilterDto;
use App\Http\Resources\ExportResources\PickingBatchReportExportResource;
use App\Http\Resources\PickingBatchReportTableResource;
use App\Models\PickingBatch;
use Illuminate\Contracts\Support\Arrayable;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\Request;
use Illuminate\Support\Arr;
use Illuminate\Support\Carbon;

class PickingBatchReport extends Report
{
    protected $reportId = 'picking_batch';
    protected $dataTableResourceClass = PickingBatchReportTableResource::class;
    protected $exportResourceClass = PickingBatchReportExportResource::class;

    protected function reportTitle()
    {
        return __('Picking Batch Report');
    }

    protected function getQuery(Request $request): ?Builder
    {
        $tableColumns = $request->get('columns');
        $columnOrder = $request->get('order');
        $sortColumnName = 'picking_batches.created_at';
        $sortDirection = 'desc';
        $filterInputs = $request->get('filter_form');

        $customerIds = app('user')->getSelectedCustomers()->pluck('id')->toArray();

        $filterCustomerId = Arr::get($filterInputs, 'customer_id');

        if ($filterCustomerId && $filterCustomerId != 'all') {
            $customerIds = array_intersect($customerIds, [$filterCustomerId]);
        }

        if (!empty($columnOrder)) {
            $sortColumnName = $tableColumns[$columnOrder[0]['column']]['name'];
            $sortDirection = $columnOrder[0]['dir'];
        }

        $query = PickingBatch::with([
                'tasks.user.contactInformation',
                'pickingBatchItemsWithTrashed.toteOrderItems',
                'pickingBatchItemsWithTrashed.orderItem.order',
            ])
            ->whereIn('customer_id', $customerIds)
            ->when(!empty($filterInputs), static function (Builder $query) use ($filterInputs) {
                if (Arr::get($filterInputs, 'start_date') || Arr::get($filterInputs, 'end_date')) {
                    $startDate = Carbon::parse($filterInputs['start_date'] ?? '1970-01-01')->startOfDay();
                    $endDate = Carbon::parse($filterInputs['end_date'] ?? Carbon::now())->endOfDay();

                    $query->whereBetween('picking_batches.created_at', [$startDate, $endDate]);
                }

                if (Arr::get($filterInputs, 'user_id') ) {
                    $query->whereHas('pickingBatchItems', function ($q) use ($filterInputs) {
                        $q->whereHas('toteOrderItems', function ($q2) use ($filterInputs) {
                            return $q2->where('user_id', '=', $filterInputs['user_id']);
                        });
                    });
                }

                if (Arr::get($filterInputs, 'active') == 'yes' ) {
                    $query->whereHas('pickingBatchItemsNotFinished');
                }

                if (Arr::get($filterInputs, 'active') == 'no' ) {
                    $query->whereDoesntHave('pickingBatchItemsNotFinished');
                }
            })
            ->select('picking_batches.*')
            ->groupBy('picking_batches.id')
            ->orderBy($sortColumnName, $sortDirection);

            if (isset($filterInputs['show_cancelled'])) {
                if ($filterInputs['show_cancelled'] == 1) {
                    $query->onlyTrashed();
                } elseif ($filterInputs['show_cancelled'] == 2) {
                    $query->withTrashed();
                }
            }

        return $query;
    }

    protected function getFilterDto(Request $request): ?Arrayable
    {
        $users = [];
        $customers = app('user')->getCustomers();

        foreach ($customers as $customer) {
            foreach (app()->user->getCustomerUsers($customer)->all() as $customerUser) {
                $users[] = $customerUser;
            }
        }

        $users = collect($users)->unique('id');

        return new PickingBatchReportFilterDto($users);
    }
}
