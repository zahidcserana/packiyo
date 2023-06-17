<?php

namespace App\Http\Controllers;

use App\Components\InventoryLogComponent;
use App\Http\Dto\Filters\{LocationsDataTableDto, ProductLocationsDataTableDto};
use App\Http\Requests\Location\{StoreRequest, UpdateRequest};
use App\Http\Requests\Csv\{ExportCsvRequest, ImportCsvRequest};
use App\Http\Requests\LocationProduct\{ExportInventoryRequest, ImportInventoryRequest};
use App\Http\Resources\{LocationTableResource, ProductLocationTableResource};
use App\Models\{Location, LocationType, Product, Warehouse};
use Illuminate\Http\{JsonResponse, RedirectResponse, Request};
use Illuminate\Support\{Facades\Cache, Facades\View};

class LocationController extends Controller
{
    public function __construct()
    {
        $this->authorizeResource(Location::class);
        $this->middleware('3pl')->only(['store', 'create', 'edit', 'update', 'storeWarehouseLocation', 'destroy', 'updateLocationProductQuantity', 'transfer']);
    }

    public function index()
    {
        $customerIds = app('user')->getSelectedCustomers()->pluck('id')->toArray();

        $data = new LocationsDataTableDto(
            Warehouse::whereIn('customer_id', $customerIds)->get(),
            LocationType::whereIn('customer_id', $customerIds)->get(),
        );

        return view('locations.index', [
            'page' => 'locations',
            'data' => $data,
            'datatableOrder' => app()->editColumn->getDatatableOrder('locations'),
            'customer' => app()->user->getSelectedCustomers()
        ]);
    }

    public function dataTable(Request $request): JsonResponse
    {
        $tableColumns = $request->get('columns');
        $columnOrder = $request->get('order');
        $sortColumnNames = 'locations.updated_at';
        $sortDirection = 'desc';

        $filterInputs = $request->get('filter_form');

        $customers = app('user')->getSelectedCustomers();

        if (!empty($columnOrder)) {
            $sortColumnNames = $tableColumns[$columnOrder[0]['column']]['name'];
            $sortDirection = $columnOrder[0]['dir'];
        }

        $sortColumnNames = explode(',', $sortColumnNames);

        $locationCollection = app('location')->getQuery($customers, $filterInputs);

        if (!empty($request->get('from_date'))) {
            $locationCollection = $locationCollection->where('locations.updated_at', '>=', $request->get('from_date'));
        }

        foreach ($sortColumnNames as $sortColumnName) {
            $locationCollection = $locationCollection->orderBy(trim($sortColumnName), $sortDirection);
        }

        $term = $request->get('search')['value'];

        if ($term) {
            $locationCollection = app('location')->searchQuery($term, $locationCollection);
        }

        if ($request->get('length') && ((int) $request->get('length')) !== -1) {
            $locationCollection = $locationCollection->skip($request->get('start'))->limit($request->get('length'));
        }

        $locations = $locationCollection->get();
        $locationCollection = LocationTableResource::collection($locations);
        $visibleFields = app('editColumn')->getVisibleFields('locations');

        return response()->json([
            'data' => $locationCollection,
            'visibleFields' => $visibleFields,
            'recordsTotal' => PHP_INT_MAX,
            'recordsFiltered' => PHP_INT_MAX
        ]);
    }

    public function productLocationDataTable(Request $request): JsonResponse
    {
        $tableColumns = $request->get('columns');
        $columnOrder = $request->get('order');
        $sortColumnName = 'products.id';
        $sortDirection = 'desc';
        $filterInputs =  $request->get('filter_form');

        if (!empty($columnOrder)) {
            $sortColumnName = $tableColumns[$columnOrder[0]['column']]['name'];
            $sortDirection = $columnOrder[0]['dir'];
        }

        $productLocationCollection = app('location')->getProductLocationQuery($filterInputs, $sortColumnName, $sortDirection);

        if (!empty($request->get('from_date'))) {
            $productLocationCollection = $productLocationCollection->where('location_product.updated_at', '>=', $request->get('from_date'));
        }

        $term = $request->get('search')['value'];

        if ($term) {
            $productLocationCollection = app('location')->searchProductLocationQuery($term, $productLocationCollection);
        }

        if ($request->get('length') && ((int) $request->get('length')) !== -1) {
            $productLocationCollection = $productLocationCollection->skip($request->get('start'))->limit($request->get('length'));
        }

        $productLocations = $productLocationCollection->get();
        $productLocationCollection = ProductLocationTableResource::collection($productLocations);
        $visibleFields = app('editColumn')->getVisibleFields('location_product');

        return response()->json([
            'data' => $productLocationCollection,
            'visibleFields' => $visibleFields,
            'recordsTotal' => PHP_INT_MAX,
            'recordsFiltered' => PHP_INT_MAX
        ]);
    }

    public function productLocations()
    {
        $customers = app()->user->getSelectedCustomers()->pluck('id')->toArray();
        $warehouses = Warehouse::whereIn('customer_id', $customers)->get();


        $data = new ProductLocationsDataTableDto(
            $warehouses,
        );

        return view('locations.productLocations', [
            'page' => 'productLocations',
            'data' => $data,
            'datatableOrder' => app()->editColumn->getDatatableOrder('location_product'),
        ]);
    }

    public function create(Warehouse $warehouse)
    {
        return view('locations.create', ['warehouse' => $warehouse]);
    }

    public function storeWarehouseLocation(StoreRequest $request, Warehouse $warehouse)
    {
        app()->location->store($request);

        return redirect()->route('warehouses.editWarehouseLocation', ['warehouse' => $warehouse])->withStatus(__('Warehouse location successfully created.'));
    }

    public function store(StoreRequest $request): JsonResponse
    {
        app()->location->store($request);

        return response()->json([
            'success' => true,
            'message' => __('Location successfully created.')
        ]);
    }

    public function edit(Request $request, Warehouse $warehouse, Location $location)
    {
        $routeName = $request->route()->getName();

        $data = ['warehouse' => $warehouse, 'location' => $location];

        if ($routeName === "warehouseLocation.edit")
        {
            return view('warehouses.updateLocation', $data);
        }

        return view('locations.edit', $data);
    }

    public function update(UpdateRequest $request, Warehouse $warehouse, Location $location): JsonResponse
    {
        app()->location->update($request, $location);

        $routeName = $request->route()->getName();

        $data = ['warehouse' => $warehouse];

        if ($routeName === "warehouseLocation.update")
        {
            return redirect()->route('warehouses.editWarehouseLocation', $data)->withStatus(__('Warehouse location successfully updated.'));
        }

        return response()->json([
            'success' => true,
            'message' => __('Location successfully updated.')
        ]);
    }

    public function transfer(Request $request, Location $location)
    {
        $product = Product::where('id', $request['product_id'])->first();

        $associateObject = Location::where('id', $request['destination_id'])->first();

        app('inventoryLog')->adjustInventory($location, $product, $request['quantity'], InventoryLogComponent::OPERATION_TYPE_TRANSFER, $associateObject);

        return redirect()->back()->withStatus(__('Transfer was successful.'));
    }

    /**
     * @param Request $request
     * @param Location $location
     * @return JsonResponse
     */
    public function updateLocationProductQuantity(Request $request, Location $location): JsonResponse
    {
        app()->location->updateLocationProducts($location, $request->location_product);

        return response()->json([
            'success' => true,
            'message' => __('Quantity successfully updated.')
        ]);
    }

    public function destroy(Request $request, Warehouse $warehouse, Location $location): RedirectResponse
    {
        app('location')->destroy(null, $location);

        $routeName = $request->route()->getName();

        $data = ['warehouse' => $warehouse, 'location' => $location];

        if ($routeName === "warehouseLocation.destroy")
        {
            return redirect()->route('warehouses.editWarehouseLocation', $data)->withStatus(__('Warehouse location successfully deleted.'));
        }

        return redirect()->back()->withStatus('Location successfully deleted');
    }

    public function filterProducts(Request $request, Location $location)
    {
        return app()->location->filterProducts($request, $location);
    }

    public function filterLocations(Request $request)
    {
        return app('location')->filterLocations($request);
    }

    public function getLocationModal(Warehouse $warehouse = null, Location $location = null): \Illuminate\Contracts\View\View
    {
        $customer = app()->user->getSelectedCustomers();

        if (is_null($location) || is_null($warehouse)) {
            return View::make('shared.modals.components.location.create', compact('customer'));
        }

        $locationProducts = $location->products()->paginate(5);

        return View::make('shared.modals.components.location.edit', compact('location', 'locationProducts', 'warehouse', 'customer'));
    }

    /**
     * @param ImportInventoryRequest $request
     * @return JsonResponse
     */
    public function importInventory(ImportInventoryRequest $request): JsonResponse
    {
        app()->location->importInventory($request);

        return response()->json([
            'success' => true,
            'message' => __('CSV successfully imported')
        ]);
    }

    /**
     * @param ExportInventoryRequest $request
     * @return mixed
     */
    public function exportInventory(ExportInventoryRequest $request)
    {
        return app('location')->exportInventory($request);
    }

    /**
     * @param ImportCsvRequest $request
     * @return JsonResponse
     */
    public function importCsv(ImportCsvRequest $request): JsonResponse
    {
        $message = app('location')->importCsv($request);

        return response()->json([
            'success' => true,
            'message' => __($message)
        ]);
    }

    /**
     * @param ExportCsvRequest $request
     * @return mixed
     */
    public function exportCsv(ExportCsvRequest $request)
    {
        return app('location')->exportCsv($request);
    }
}
