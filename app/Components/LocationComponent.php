<?php

namespace App\Components;

use App\Http\Requests\Csv\{ExportCsvRequest, ImportCsvRequest};
use App\Http\Requests\Location\{DestroyBatchRequest,
    DestroyRequest,
    StoreBatchRequest,
    StoreRequest,
    UpdateBatchRequest,
    UpdateRequest};
use App\Http\Requests\LocationProduct\{ExportInventoryRequest, ImportInventoryRequest};
use App\Http\Resources\{ExportResources\InventoryExportResource,
    ExportResources\LocationExportResource,
    LocationCollection,
    LocationResource};
use App\Models\{Customer, Location, LocationProduct, Product, Warehouse, Webhook};
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\{JsonResponse, Request, Resources\Json\ResourceCollection};
use Illuminate\Support\{Arr, Collection, Facades\Session, Str};
use Symfony\Component\HttpFoundation\StreamedResponse;

class LocationComponent extends BaseComponent
{
    public function __construct()
    {
    }

    public function store(StoreRequest $request, $fireWebhook = true)
    {
        $input = $request->validated();

        $locationArr = Arr::except($input, ['location_product']);

        $location = Location::create($locationArr);

        $locationProducts = Arr::get($input, 'location_product');

        if(!is_null($locationProducts)) {
            foreach ($locationProducts as $locationProduct) {
                if (empty($locationProduct['product_id']) ) {
                    continue;
                }

                $product = Product::find(Arr::get($locationProduct, 'product_id'));

                app('inventoryLog')->adjustInventory(
                    $location,
                    $product,
                    Arr::get($locationProduct, 'quantity_on_hand'),
                    InventoryLogComponent::OPERATION_TYPE_MANUAL
                );
            }
        }

        if ($fireWebhook) {
            $this->webhook(new LocationResource($location), Location::class, Webhook::OPERATION_TYPE_STORE, $location->warehouse->customer_id);
        }

        return $location;
    }

    public function storeBatch(StoreBatchRequest $request): Collection
    {
        $responseCollection = new Collection();

        $input = $request->validated();

        foreach ($input as $record) {
            $storeRequest = StoreRequest::make($record);
            $responseCollection->add($this->store($storeRequest, false));
        }

        $this->batchWebhook($responseCollection, Location::class, LocationCollection::class, Webhook::OPERATION_TYPE_STORE);

        return $responseCollection;
    }

    public function update(UpdateRequest $request, Location $location, $fireWebhook = true): Location
    {
        $input = $request->validated();

        if (isset($input['priority_counting_requested_at']) && !$location->priority_counting_requested_at) {
            $input['priority_counting_requested_at'] = now();
        } else {
            $input['priority_counting_requested_at'] = null;
        }

        if (isset($input['location_product'])) {
            $this->updateLocationProducts($location, Arr::get($input, 'location_product'));
        }

        Arr::forget($input, 'location_product');

        $location->update($input);

        if ($fireWebhook) {
            $this->webhook(new LocationResource($location), Location::class, Webhook::OPERATION_TYPE_UPDATE, $location->warehouse->customer_id);
        }

        return $location;
    }

    public function updateBatch(UpdateBatchRequest $request): Collection
    {
        $responseCollection = new Collection();

        $input = $request->validated();

        foreach ($input as $record) {
            $updateRequest = UpdateRequest::make($record);
            $location = Location::where('id', $record['id'])->where('warehouse_id', $record['warehouse_id'])->first();

            $responseCollection->add($this->update($updateRequest, $location, false));
        }

        $this->batchWebhook($responseCollection, Location::class, LocationCollection::class, Webhook::OPERATION_TYPE_UPDATE);

        return $responseCollection;
    }

    public function updateLocationProducts(Location $location, $locationProducts): void
    {
        foreach ($locationProducts as $item) {
            if (Arr::exists($item, 'product_id')) {
                app('inventoryLog')->adjustInventory(
                    $location,
                    Product::find(Arr::get($item, 'product_id')),
                    Arr::get($item, 'quantity_on_hand'),
                    InventoryLogComponent::OPERATION_TYPE_MANUAL
                );
            }

            if (Arr::get($item, 'delete')) {
                $productLocation = LocationProduct::find(Arr::get($item, 'location_product_id'));
                if ($productLocation) {
                    $productLocation->delete();
                }
            }
        }
    }

    public function destroy(?DestroyRequest $request, Location $location): array
    {
        foreach ($location->products as $locationProduct) {
            app('inventoryLog')->adjustInventory(
                $location,
                $locationProduct->product,
                0,
                InventoryLogComponent::OPERATION_TYPE_MANUAL
            );
        }

        $location->delete();

        return ['name' => $location->name, 'customer_id' => $location->warehouse->customer_id];
    }

    public function destroyBatch(DestroyBatchRequest $request): Collection
    {
        $responseCollection = new Collection();
        $input = $request->validated();

        foreach ($input as $record) {
            $destroyRequest = DestroyRequest::make($record);
            $location = Location::where('id', $record['id'])->first();

            $responseCollection->add($this->destroy($destroyRequest, $location, false));
        }

        $this->batchWebhook($responseCollection, Location::class, ResourceCollection::class, Webhook::OPERATION_TYPE_DESTROY);

        return $responseCollection;
    }

    public function filterProducts(Request $request, Location $location)
    {
        $term = $request->get('term');

        $results = [];
        $productIds = [];

        $customers = Collection::make(Customer::find($request->get('customer')));

        if ($customers->isEmpty()) {
            $customers = app('user')->getSelectedCustomers();
        }

        if ($customers->isNotEmpty() && $term) {
            $term = $term . '%';
            $products = Product::whereIn('customer_id', $customers->pluck('id')->toArray())
                ->where(static function ($q) use ($term) {
                    $q->where('name', 'like', $term)
                        ->orWhere('sku', 'like', $term);
                })
                ->get();

            foreach ($products as $product) {
                $results[] = [
                    'id' => $product->id,
                    'text' => 'SKU: ' . $product->sku . ', NAME:' . $product->name
                ];
            }

            return response()->json([
                'results' => $results
            ]);
        }

        return response()->json([
            'results' => []
        ]);
    }

    public function filterLocations(Request $request): JsonResponse
    {
        $term = $request->get('term');
        $product = $request->get('product_id');
        $results = [];

        $customers = app('user')->getSelectedCustomers()->pluck('id')->toArray();

        if ($term) {
            $term = $term . '%';

            $locations = Location::join('warehouses', 'locations.warehouse_id', '=', 'warehouses.id')
                ->whereIn('warehouses.customer_id', $customers)
                ->where(function ($query) use ($term) {
                    $query->where('locations.name', 'like', $term);
                })
                ->select('locations.*')
                ->get();

            foreach ($locations as $location) {
                $results[] = [
                    'id' => $location->id,
                    'text' => __(':locationName, pickable - :pickable, sellable - :sellable', [
                            'locationName' => $location->name,
                            'pickable' => $location->isPickableLabel(),
                            'sellable' => $location->isSellableLabel(),
                        ])
                ];
            }
        }

        return response()->json([
            'results' => $results
        ]);
    }

    /**
     * @param ImportInventoryRequest $request
     * @return string
     */
    public function importInventory(ImportInventoryRequest $request): string
    {
        $input = $request->validated();

        $warehouse = Warehouse::find($input['warehouse_id']);
        $customer = Customer::find($input['customer_id']);

        $importLines = app('csv')->getCsvData($input['inventory_csv']);

        $columns = array_intersect(
            app('csv')->unsetCsvHeader($importLines, 'sku'),
            InventoryExportResource::columns()
        );

        if (!empty($importLines)) {
            foreach ($importLines as $importLineIndex => $importLine) {
                $data = [];

                foreach ($columns as $columnIndex => $column) {
                    if (Arr::has($importLine, $columnIndex)) {
                        $data[$column] = Arr::get($importLine, $columnIndex);
                    }
                }

                $product = $customer->products()->where('sku', $data['sku'])->first();
                $location = $warehouse->locations()->where('name', $data['location'])->first();
                $quantity = (int)$data['quantity'];

                if ($product && $location) {
                    app('inventoryLog')->adjustInventory(
                        $location,
                        $product,
                        $quantity,
                        InventoryLogComponent::OPERATION_TYPE_MANUAL
                    );
                }

                Session::flash('status', ['type' => 'info', 'message' => __('Importing :current/:total inventory lines', ['current' => $importLineIndex + 1, 'total' => count($importLines)])]);
                Session::save();
            }
        }

        Session::flash('status', ['type' => 'success', 'message' => __('Inventory was successfully imported!')]);

        return __('Inventory was successfully imported!');
    }

    /**
     * @param ExportInventoryRequest $request
     * @return StreamedResponse
     */
    public function exportInventory(ExportInventoryRequest $request): StreamedResponse
    {
        $input = $request->validated();
        $search = $input['search']['value'];

        $locationProducts = $this->getProductLocationQuery($request->get('filter_form'));

        if ($search) {
            $locationProducts = $this->searchProductLocationQuery($search, $locationProducts);
        }

        $csvFileName = Str::kebab(auth()->user()->contactInformation->name) . '-inventory-export.csv';

        return app('csv')->export($request, $locationProducts->get(), InventoryExportResource::columns(), $csvFileName, InventoryExportResource::class);
    }

    /**
     * @param ImportCsvRequest $request
     * @return string
     */
    public function importCsv(ImportCsvRequest $request): string
    {
        $input = $request->validated();

        $importLines = app('csv')->getCsvData($input['import_csv']);

        $columns = array_intersect(
            app('csv')->unsetCsvHeader($importLines, 'name'),
            LocationExportResource::columns()
        );

        if (!empty($importLines)) {
            $storedCollection = new Collection();
            $updatedCollection = new Collection();

            $locationsToImport = [];

            foreach ($importLines as $importLine) {
                $data = [];
                $data['customer_id'] = $input['customer_id'];

                foreach ($columns as $columnsIndex => $column) {
                    if (Arr::has($importLine, $columnsIndex)) {
                        $data[$column] = Arr::get($importLine, $columnsIndex);
                    }
                }

                if (!Arr::has($locationsToImport, $data['name'])) {
                    $locationsToImport[$data['name']] = [];
                }

                $locationsToImport[$data['name']][] = $data;
            }

            $locationToImportIndex = 0;

            foreach ($locationsToImport as $locationToImport) {
                $warehouse = Warehouse::with([
                    'locations' => function($query) use ($locationToImport) {
                        $query->where('name', $locationToImport[0]['name']);
                    },
                    'contactInformation',
                    'customer'
                ])->whereHas('contactInformation', static function($query) use ($locationToImport) {
                    $query->where('name', $locationToImport[0]['warehouse']);
                })
                ->where('customer_id', $locationToImport[0]['customer_id'])->first();

                if($warehouse) {
                    $location = $warehouse->locations->first();
                    $locationToImport[0]['warehouse_id'] = $warehouse->id;

                    if ($location) {
                        $updatedCollection->add($this->update($this->createRequestFromImport($locationToImport, $location, true), $location,false));
                    } else {
                        $storedCollection->add($this->store($this->createRequestFromImport($locationToImport), $location));
                    }

                    Session::flash('status', ['type' => 'info', 'message' => __('Importing :current/:total locations', ['current' => ++$locationToImportIndex , 'total' => count($locationsToImport)])]);
                    Session::save();
                }
            }

            $this->batchWebhook($storedCollection, Location::class, LocationCollection::class, Webhook::OPERATION_TYPE_STORE);
            $this->batchWebhook($updatedCollection, Location::class, LocationCollection::class, Webhook::OPERATION_TYPE_UPDATE);
        }

        Session::flash('status', ['type' => 'success', 'message' => __('Locations were successfully imported!')]);

        return __('Locations were successfully imported!');
    }

    /**
     * @param ExportCsvRequest $request
     * @return StreamedResponse
     */
    public function exportCsv(ExportCsvRequest $request): StreamedResponse
    {
        $input = $request->validated();
        $search = $input['search']['value'];

        $customers = app('user')->getSelectedCustomers();

        $locations = $this->getQuery($customers, $request->get('filter_form'));

        if ($search) {
            $locations = $this->searchQuery($search, $locations);
        }

        $csvFileName = Str::kebab(auth()->user()->contactInformation->name) . '-locations-export.csv';

        return app('csv')->export($request, $locations->get(), LocationExportResource::columns(), $csvFileName, LocationExportResource::class);
    }

    /**
     * @param $filterInputs
     * @param string $sortColumnName
     * @param string $sortDirection
     * @param $customers
     * @return mixed
     */
    public function getProductLocationQuery($filterInputs, string $sortColumnName = 'products.id', string $sortDirection = 'desc')
    {
        $customerIds = app('user')->getSelectedCustomers()->pluck('id')->toArray();

        $filterCustomerId = Arr::get($filterInputs, 'customer_id');

        if ($filterCustomerId && $filterCustomerId != 'all') {
            $customerIds = array_intersect($customerIds, [$filterCustomerId]);
        }

        $productLocationCollection = LocationProduct::join('products', 'products.id', '=', 'location_product.product_id')
            ->join('locations', 'locations.id', '=', 'location_product.location_id')
            ->whereIn('products.customer_id', $customerIds)
            ->where(function ($query) use ($filterInputs) {
                // Find by filter result
                // Warehouse
                if ($filterInputs['warehouse']) {
                    $query->where('locations.warehouse_id', $filterInputs['warehouse']);
                }

                // Sellable
                if (isset($filterInputs['sellable'])) {
                    $query->where('locations.sellable', $filterInputs['sellable']);
                }

                // Pickable
                if (isset($filterInputs['pickable'])) {
                    $query->where('locations.pickable', $filterInputs['pickable']);
                }
            })
            ->select('location_product.*')
            ->orderBy($sortColumnName, $sortDirection);

        return $productLocationCollection;
    }

    /**
     * @param string $term
     * @param $productLocationCollection
     * @return mixed
     */
    public function searchProductLocationQuery(string $term, $productLocationCollection)
    {
        $term = $term . '%';

        return $productLocationCollection->where(static function(Builder $query) use ($term) {
            $query->orWhere('locations.name', 'like', $term)
                ->orWhere('products.name', 'like', $term)
                ->orWhere('products.sku', 'like', $term);
        });
    }

    public function searchQuery(string $term, $locationCollection)
    {
        $term = $term . '%';

        return $locationCollection->where(function ($q) use ($term) {
            $q->whereHas('warehouse.contactInformation', function($query) use ($term) {
                $query->where('name', 'like', $term)
                    ->orWhere('address', 'like', $term)
                    ->orWhere('city', 'like', $term)
                    ->orWhere('zip', 'like', $term)
                    ->orWhere('email', 'like', $term)
                    ->orWhere('phone', 'like', $term);
            })
            ->orWhere('locations.name', 'like', $term);
        });
    }

    public function getQuery($customers, $filterInputs)
    {
        $customers = $customers->pluck('id')->toArray();

        $locationCollection = Location::join('warehouses', 'locations.warehouse_id', '=', 'warehouses.id')
            ->join('contact_informations', 'warehouses.id', '=', 'contact_informations.object_id')
            ->leftJoin('location_types', 'locations.location_type_id', '=', 'location_types.id')
            ->where('contact_informations.object_type', Warehouse::class)
            ->whereIn('warehouses.customer_id', $customers)
            ->where(function ($query) use ($filterInputs) {
                // Find by filter result
                // Warehouse
                if (Arr::get($filterInputs, 'warehouse')) {
                    $query->where('locations.warehouse_id', $filterInputs['warehouse']);
                }

                // Location type
                if (Arr::get($filterInputs, 'location_type')) {
                    $query->where('locations.location_type_id', $filterInputs['location_type']);
                }

                // Sellable
                $sellable = Arr::get($filterInputs, 'sellable');
                if (!is_null($sellable)) {
                    $query->where('locations.sellable', $sellable);
                }

                // Pickable
                $pickable = Arr::get($filterInputs, 'pickable');
                if (!is_null($pickable)) {
                    $query->where('locations.pickable', $pickable);
                }

                if (!is_null($filterInputs) && !empty($filterInputs['customer']) && $filterInputs['customer'] !== 0) {
                    $query->where('warehouses.customer_id', $filterInputs['customer']);
                }
            })
            ->select('locations.*')
            ->groupBy('locations.id');

        return $locationCollection;
    }

    /**
     * @param array $data
     * @param Location|null $location
     * @param bool $update
     * @return StoreRequest|UpdateRequest
     */
    private function createRequestFromImport(array $data, Location $location = null, bool $update = false)
    {
        $requestData = [
            'name' => $data[0]['name'],
            'warehouse_id' => $data[0]['warehouse_id'],
            'pickable' => strtolower($data[0]['pickable']) == 'yes' ? true : false,
            'sellable' => strtolower($data[0]['sellable']) == 'yes' ? true : false,
            'disabled_on_picking_app' => strtolower(Arr::get($data, '0.disabled_on_picking_app')) == 'yes' ? true : false,
        ];

        if (Arr::has($data, '0.barcode')) {
            $requestData['barcode'] = Arr::get($data, '0.barcode');
        }

        if ($update) {
            $requestDataID = [
                'id' => $location->id
            ];
            $requestData = array_merge($requestDataID, $requestData);
        }

        return $update ? UpdateRequest::make($requestData) : StoreRequest::make($requestData);
    }
}
