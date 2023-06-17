<?php

namespace App\Http\Controllers;

use App\Http\Requests\Warehouses\DestroyRequest;
use App\Http\Requests\Warehouses\StoreRequest;
use App\Http\Requests\Warehouses\UpdateRequest;
use App\Http\Resources\WarehouseTableResource;
use App\Models\Customer;
use App\Models\Warehouse;
use Carbon\Carbon;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\View;

class WarehouseController extends Controller
{
    public function __construct()
    {
        $this->authorizeResource(Warehouse::class);
        $this->middleware('3pl')->only(['store', 'create', 'edit', 'update', 'destroy']);
    }

    public function index()
    {
        $customer = app()->user->getSelectedCustomers();

        return view('warehouses.index', [
            'page' => 'warehouses',
            'datatableOrder' => app()->editColumn->getDatatableOrder('warehouses'),
            'customer' => $customer
        ]);
    }

    public function dataTable(Request $request): JsonResponse
    {
        $tableColumns = $request->get('columns');
        $columnOrder = $request->get('order');
        $sortColumnName = 'warehouses.id';
        $sortDirection = 'desc';

        if (!empty($columnOrder)) {
            $sortColumnName = $tableColumns[$columnOrder[0]['column']]['name'] ?? 'warehouses.id';
            $sortDirection = $columnOrder[0]['dir'];
        }

        $customers = app()->user->getSelectedCustomers()->pluck('id')->toArray();


        $warehouseCollection = Warehouse::join('customers', 'warehouses.customer_id', '=', 'customers.id')
            ->join('contact_informations AS customer_contact_information', 'customers.id', '=', 'customer_contact_information.object_id')
            ->join('contact_informations', 'warehouses.id', '=', 'contact_informations.object_id')
            ->where('customer_contact_information.object_type', Customer::class)
            ->where('contact_informations.object_type', Warehouse::class)
            ->select('warehouses.*')
            ->groupBy('warehouses.id')
            ->orderBy($sortColumnName, $sortDirection);

        $warehouseCollection = $warehouseCollection->whereIn('warehouses.customer_id', $customers);

        $term = $request->get('search')['value'];

        if ($term) {
            // TODO: sanitize term
            $term = $term . '%';

            $warehouseCollection->where(function ($q) use ($term) {
                $q->whereHas('contactInformation', function($query) use ($term) {
                    $query->where('name', 'like', $term)
                        ->orWhere('address', 'like', $term)
                        ->orWhere('city', 'like', $term)
                        ->orWhere('zip', 'like', $term)
                        ->orWhere('email', 'like', $term)
                        ->orWhere('phone', 'like', $term);
                })
                    ->orWhereHas('customer.contactInformation', function($query) use ($term) {
                        $query->where('name', 'like', $term);
                    });
            });
        }

        if ($request->get('length') && ((int) $request->get('length')) !== -1) {
            $warehouseCollection = $warehouseCollection->skip($request->get('start'))->limit($request->get('length'));
        }

        $warehouses = $warehouseCollection->get();

        $warehouseCollection = WarehouseTableResource::collection($warehouses);

        $visibleFields = app('editColumn')->getVisibleFields('warehouses');

        return response()->json([
            'data' => $warehouseCollection,
            'visibleFields' => $visibleFields,
            'recordsTotal' => PHP_INT_MAX,
            'recordsFiltered' => PHP_INT_MAX
        ]);
    }

    public function create()
    {
        return view('warehouses.create');
    }

    public function store(StoreRequest $request): JsonResponse
    {
        app()->warehouse->store($request);

        return response()->json([
            'success' => true,
            'message' => __('Warehouse successfully created.')
        ]);
    }

    public function edit(Request $request, Warehouse $warehouse)
    {
        $allCustomers = Customer::all();
        $routeName = $request->route()->getName();

        $data = ['warehouse' => $warehouse, 'allUsers' => $allCustomers];

        if ($routeName === "warehouses.editWarehouseLocation")
        {
            return view('warehouses.editLocation', $data);
        }

        return view('warehouses.warehouseEdit', $data);
    }

    public function update(UpdateRequest $request, Warehouse $warehouse): JsonResponse
    {
        app()->warehouse->update($request, $warehouse);

        return response()->json([
            'success' => true,
            'message' => __('Warehouse successfully updated.')
        ]);
    }

    public function destroy(DestroyRequest $request, Warehouse $warehouse)
    {
        app()->warehouse->destroy($request, $warehouse);

        return redirect()->back()->withStatus('Warehouse successfully deleted.');
    }

    public function addCustomers(Request $request, Warehouse $warehouse)
    {
        app()->warehouse->addCustomers($request, $warehouse);

        return redirect()->back()->withStatus(__('Warehouse successfully updated.'));
    }

    public function filterCustomers(Request $request)
    {
        return app()->warehouse->filterCustomers($request);
    }

    public function getWarehouseModal(Warehouse $warehouse = null): \Illuminate\Contracts\View\View
    {
        if (is_null($warehouse)) {
            return View::make('shared.modals.components.warehouse.create');
        }

        return View::make('shared.modals.components.warehouse.edit', compact('warehouse'));
    }
}
