<?php

namespace App\Reports;

use App\Http\Resources\ExportResources\PickerReportExportResource;
use App\Http\Resources\PickerReportTableResource;
use App\Models\Task;
use App\Models\TaskType;
use App\Models\User;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\Request;
use Illuminate\Support\Arr;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;

class PickerReport extends Report
{
    protected $reportId = 'picker';
    protected $dataTableResourceClass = PickerReportTableResource::class;
    protected $exportResourceClass = PickerReportExportResource::class;

    protected function reportTitle()
    {
        return __('Pickers Report');
    }

    protected function getQuery(Request $request): ?Builder
    {
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

        $query = Task::query()->with(['user.contactInformation'])
            ->leftJoin('task_types', 'tasks.task_type_id', '=', 'task_types.id')
            ->leftJoin('picking_batches', 'picking_batches.id', '=', 'tasks.taskable_id')
            ->leftJoin('picking_batch_items', function ($join) {
                $join->on('picking_batches.id', '=', 'picking_batch_items.picking_batch_id')
                    ->where('picking_batch_items.quantity_picked', '>', 0);

            })
            ->leftJoin('order_items', 'picking_batch_items.order_item_id', '=', 'order_items.id')
            ->leftJoin('contact_informations', 'tasks.user_id', '=', 'contact_informations.object_id')
            ->where('contact_informations.object_type', User::class)
            ->whereIn('task_types.customer_id', $customerIds)
            ->where('task_types.type', TaskType::TYPE_PICKING)
            ->when(!empty($filterInputs), static function (Builder $query) use ($filterInputs) {
                if (Arr::get($filterInputs, 'start_date') || Arr::get($filterInputs, 'end_date')) {
                    $startDate = Carbon::parse($filterInputs['start_date'] ?? '1970-01-01')->startOfDay();
                    $endDate = Carbon::parse($filterInputs['end_date'] ?? Carbon::now())->endOfDay();

                    $query->whereBetween('picking_batch_items.updated_at', [$startDate, $endDate]);
                }
            })
            ->when(!empty($term), static function (Builder $query) use ($term) {
                $term = $term . '%';

                $query->whereHas('user.contactInformation', static function (Builder $query) use ($term) {
                    $query->where('name', 'like', $term);
                });
            })
            ->select('tasks.user_id')
            ->addSelect(DB::raw('SUM(picking_batch_items.quantity_picked) as items_count'))
            ->addSelect(DB::raw('count(DISTINCT(order_items.product_id)) as unique_items_count'))
            ->addSelect(DB::raw('count(DISTINCT(order_items.order_id)) as orders_count'))
            ->groupBy('tasks.user_id')
            ->orderBy($sortColumnName, $sortDirection);

        if ($request->get('length') && ((int) $request->get('length')) !== -1) {
            $query = $query->skip($request->get('start'))->limit($request->get('length'));
        }

        return $query;
    }
}
