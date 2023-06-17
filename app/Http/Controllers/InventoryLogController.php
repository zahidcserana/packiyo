<?php

namespace App\Http\Controllers;

use App\Http\Dto\Filters\InventoryChangeLogDataTableDto;
use App\Http\Requests\Csv\ExportCsvRequest;
use App\Http\Resources\InventoryLogTableResource;
use App\Models\{Customer, Warehouse};
use Illuminate\Http\{JsonResponse, Request};
use Illuminate\Support\{Facades\Cache, Collection};
use Illuminate\Contracts\{Foundation\Application, View\Factory, View\View};

class InventoryLogController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return Factory|Application|View
     */
    public function index()
    {
        $customers = app()->user->getSelectedCustomers()->pluck('id')->toArray();

        $users = new Collection();

        foreach (app()->user->getSelectedCustomers() as $customer) {
            foreach ($customer->users as $user) {
                $users[] = $user;
            }
        }

        $data = new InventoryChangeLogDataTableDto(
            Customer::whereHas('contactInformation')->get(),
            Warehouse::whereIn('customer_id', $customers)->whereHas('contactInformation')->get(),
            app()->inventoryLog::REASONS,
            $users->unique()
        );

        return view('inventory_log.index', [
            'data' => $data,
            'datatableOrder' => app()->editColumn->getDatatableOrder('inventory_logs'),
        ]);
    }

    public function dataTable(Request $request): JsonResponse
    {
        $tableColumns = $request->get('columns');
        $columnOrder = $request->get('order');
        $sortColumnName = 'inventory_logs.created_at';
        $sortDirection = 'desc';
        $filterInputs =  $request->get('filter_form');

        if (!empty($columnOrder)) {
            $sortColumnName = $tableColumns[$columnOrder[0]['column']]['name'];
            $sortDirection = $columnOrder[0]['dir'];
        }

        $inventoryLogCollection = app('inventoryLog')->getQuery($filterInputs, $sortColumnName, $sortDirection);

        $term = $request->get('search')['value'];

        if ($term) {
            $inventoryLogCollection = app('inventoryLog')->searchQuery($term, $inventoryLogCollection);
        }

        if ($request->get('length') && ((int) $request->get('length')) !== -1) {
            $inventoryLogCollection = $inventoryLogCollection->skip($request->get('start'))->limit($request->get('length'));
        }

        $inventoryLogs = $inventoryLogCollection->get();

        $inventoryLogCollection = InventoryLogTableResource::collection($inventoryLogs);

        return response()->json([
            'data' => $inventoryLogCollection,
            'visibleFields' => app('editColumn')->getVisibleFields('inventory_logs'),
            'recordsTotal' => PHP_INT_MAX,
            'recordsFiltered' => PHP_INT_MAX
        ]);

    }

    /**
     * @param ExportCsvRequest $request
     * @return mixed
     */
    public function exportInventory(ExportCsvRequest $request)
    {
        return app('inventoryLog')->exportInventory($request);
    }
}
