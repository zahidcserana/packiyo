<?php

namespace App\Components;

use App\Http\Requests\{Csv\ExportCsvRequest,
    Csv\ImportCsvRequest,
    Tote\DestroyBatchRequest,
    Tote\DestroyRequest,
    Tote\PickOrderItemsByBarcodeRequest,
    Tote\PickOrderItemsRequest,
    Tote\StoreBatchRequest,
    Tote\StoreRequest,
    Tote\UpdateBatchRequest,
    Tote\UpdateRequest};
use App\Http\Resources\{ExportResources\TotesExportResource, ToteCollection, ToteResource};
use App\Models\{Customer,
    Location,
    Order,
    OrderItem,
    OrderLock,
    PickingCart,
    Product,
    Tote,
    ToteOrderItem,
    Warehouse,
    Webhook};
use Carbon\Carbon;
use Illuminate\Contracts\Foundation\Application;
use Illuminate\Contracts\Translation\Translator;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\{JsonResponse, Request};
use Illuminate\Support\{Arr, Collection, Facades\DB, Facades\Session, Str};
use PDF;
use Picqer\Barcode\BarcodeGeneratorPNG;

class ToteComponent extends BaseComponent
{
    public const TOTE_PREFIX = 'TOT-';

    public function store(StoreRequest $request, $fireWebhook = true)
    {
        $input = $request->validated();

        $numberOfTotes = (int)$input['number_of_totes'];

        $namePrefix = $input['name_prefix'] ?? self::TOTE_PREFIX;

        for ($i = 0; $i < $numberOfTotes; $i++) {
            $toteName = $numberOfTotes === 1 ? $namePrefix : Tote::getUniqueIdentifier($namePrefix, $input['warehouse_id']);

            $tote = Tote::create([
                'warehouse_id' => $input['warehouse_id'],
                'name' => $toteName,
                'barcode' => $input['barcode'] ?? $toteName,
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now()
            ]);

            if ($fireWebhook) {
                $this->webhook(new ToteResource($tote), Tote::class, Webhook::OPERATION_TYPE_STORE, $tote->warehouse->customer_id);
            }
        }


        return $tote ?? null;
    }

    public function storeBatch(StoreBatchRequest $request): Collection
    {
        $responseCollection = new Collection();

        $input = $request->validated();

        foreach ($input as $record) {
            $storeRequest = StoreRequest::make($record);
            $responseCollection->add($this->store($storeRequest, false));
        }

        $this->batchWebhook($responseCollection, Tote::class, ToteCollection::class, Webhook::OPERATION_TYPE_STORE);

        return $responseCollection;
    }

    public function update(UpdateRequest $request, Tote $tote, $fireWebhook = true): Tote
    {
        $input = $request->validated();

        $tote->update($input);

        if ($fireWebhook) {
            $this->webhook(new ToteResource($tote), Tote::class, Webhook::OPERATION_TYPE_UPDATE, $tote->warehouse->id);
        }

        return $tote;
    }

    public function updateBatch(UpdateBatchRequest $request): Collection
    {
        $responseCollection = new Collection();

        $input = $request->validated();

        foreach ($input as $record) {
            $updateRequest = UpdateRequest::make($record);
            $tote = Tote::where('barcode', $record['barcode'])->first();

            $responseCollection->add($this->update($updateRequest, $tote, false));
        }

        $this->batchWebhook($responseCollection, Tote::class, ToteCollection::class, Webhook::OPERATION_TYPE_UPDATE);

        return $responseCollection;
    }

    /**
     * @throws \Exception
     */
    public function destroy(DestroyRequest $request): bool
    {
        $input = $request->validated();

        $tote = Tote::whereId($input['id'])->firstOrFail();

        if (count($tote->placedToteOrderItems) === 0) {
            $tote->delete();

            return true;
        }

        return false;
    }

    /**
     * @throws \Exception
     */
    public function destroyBatch(DestroyBatchRequest $request): Collection
    {
        $responseCollection = new Collection();

        $input = $request->validated();

        foreach ($input as $record) {
            $tote = Tote::find($record['id']);

            if ($tote) {
                $destroyRequest = DestroyRequest::make($record);
                $responseCollection->add($this->destroy($destroyRequest));
            }
        }

        $this->batchWebhook($responseCollection, Tote::class, ToteCollection::class, Webhook::OPERATION_TYPE_DESTROY);

        return $responseCollection;
    }

    public function clearTote(Tote $tote)
    {
        $orderIds = $tote->placedToteOrderItems->map(function($toteOrderItem) {
            return $toteOrderItem->orderItem->order->id;
        })->toArray();

        OrderLock::whereIn('order_id', $orderIds)->delete();

        foreach ($tote->placedToteOrderItems as $placedToteOrderItem) {
            $placedToteOrderItem->update([
                'quantity_removed' => DB::raw('quantity'),
                'quantity_remaining' => 0
            ]);
        }
    }

    public function toteItems(Tote $tote)
    {
        return $tote->toteOrderItems;
    }

    public function barcode(Tote $tote)
    {
        $generator = new BarcodeGeneratorPNG();

        $data = [
            'name' => $tote->name,
            'barcode' => $generator->getBarcode($tote->barcode, $generator::TYPE_CODE_128),
            'barcodeNumber' => $tote->barcode
        ];

        $paperWidth = dimension_width($tote->warehouse->customer, 'barcode');
        $paperHeight = dimension_height($tote->warehouse->customer, 'barcode');

        return PDF::loadView('pdf.barcode', $data)
            ->setPaper([0, 0, $paperWidth, $paperHeight])
            ->stream('tote_barcode.pdf');
    }

    public function printBarcodes(Request $request)
    {
        $generator = new BarcodeGeneratorPNG();
        $totes = Tote::whereIn('id', $request->totes)->get();
        $barcodes = [];

        $paperWidth = 0;

        foreach ($totes as $tote) {
            $barcodes[] = [
                'name' => $tote->name,
                'barcode' => $generator->getBarcode($tote->barcode, $generator::TYPE_CODE_128),
                'number' => $tote->barcode,
                'type' => 'Tote',
            ];

            if (!$paperWidth) {
                $paperWidth = dimension_width($tote->warehouse->customer, 'barcode');
                $paperHeight = dimension_height($tote->warehouse->customer, 'barcode');
            }
        }

        return PDF::loadView('pdf.barcodes', [
            'barcodes' => $barcodes
        ])
            ->setPaper([0, 0, $paperWidth, $paperHeight])
            ->stream('tote_barcodes.pdf');
    }

    public function filterWarehouses(Request $request): JsonResponse
    {
        $customer = app()->user->getSelectedCustomers();

        $term = $request->get('term');

        $results = [];
        if ($term) {
            $warehouses = Warehouse::whereHas('contactInformation', static function ($query) use ($term) {
                $term = $term . '%';

                $query->where('name', 'like', $term)
                    ->orWhere('company_name', 'like', $term)
                    ->orWhere('email', 'like', $term)
                    ->orWhere('zip', 'like', $term)
                    ->orWhere('city', 'like', $term)
                    ->orWhere('phone', 'like', $term);
            });

            $customers = $customer->pluck('id')->toArray();

            $warehouses = $warehouses->whereIn('customer_id', $customers);

            foreach ($warehouses->get() as $warehouse) {
                if ($warehouse->count()) {
                    $results[] = [
                        'id' => $warehouse->id,
                        'text' => $warehouse->contactInformation->name . ', ' . $warehouse->contactInformation->email . ', ' . $warehouse->contactInformation->zip . ', ' . $warehouse->contactInformation->city
                    ];
                }
            }
        }

        return response()->json([
            'results' => $results
        ]);
    }

    public function filterPickingCarts(Request $request): JsonResponse
    {
        $warehouses = [];

        foreach (app('user')->getSelectedCustomers() as $customer) {
            /** @var Customer $customer */
            foreach ($customer->warehouses as $warehouse) {
                $warehouses[] = $warehouse->id;
            }
        }
        $term = $request->get('term');

        $results = [];
        if ($term) {
            $term = $term . '%';

            $carts = PickingCart::whereIn('warehouse_id', $warehouses)
                ->where(function ($q) use ($term) {
                    return $q
                        ->where('name', 'like', $term)
                        ->orWhere('barcode', 'like', $term);
                })
                ->get();

            foreach ($carts as $cart) {
                $results[] = [
                    'id' => $cart->id,
                    'text' => $cart->name
                ];
            }
        }

        return response()->json([
            'results' => $results
        ]);
    }

    /**
     * @param PickOrderItemsRequest $request
     * @param Tote $tote
     * @return ToteOrderItem
     */
    public function pickOrderItems(PickOrderItemsRequest $request, Tote $tote): ToteOrderItem
    {
        $input = $request->validated();

        $toteOrderItem = new ToteOrderItem();
        $toteOrderItem->tote()->associate($tote);
        $toteOrderItem->quantity = $input['quantity'];
        $toteOrderItem->order_item_id = $input['order_item_id'];
        $toteOrderItem->picked_at = Carbon::now();

        $toteOrderItem->save();

        return $toteOrderItem;
    }

    /**
     * @param PickOrderItemsByBarcodeRequest $request
     * @param Tote $tote
     * @param Order $order
     * @param Location $location
     * @return ToteOrderItem|null
     */
    public function pickOrderItemsByBarcode(PickOrderItemsByBarcodeRequest $request, Tote $tote, Order $order, Location $location): ?ToteOrderItem
    {
        $input = $request->validated();

        $product = Product::where('barcode', $input['product_barcode'])->first();

        $orderItem = OrderItem::where('product_id', $product->id)->where('order_id', $order->id)->first();

        if (!is_null($orderItem)) {
            $toteOrderItem = new ToteOrderItem();
            $toteOrderItem->tote()->associate($tote);
            $toteOrderItem->quantity = $input['quantity'];
            $toteOrderItem->order_item_id = $orderItem->id;
            $toteOrderItem->picked_at = Carbon::now();
            $toteOrderItem->location()->associate($location);

            $toteOrderItem->save();

            return $toteOrderItem;
        }

        return null;
    }

    /**
     * @param $filterInputs
     * @param string $sortColumnName
     * @param string $sortDirection
     * @return \Illuminate\Database\Query\Builder
     */
    public function getToteItemsQuery($filterInputs, string $sortColumnName = 'tote_order_items.created_at', string $sortDirection = 'desc')
    {
        return ToteOrderItem::join('totes', 'tote_order_items.tote_id', '=', 'totes.id')
            ->join('order_items', 'tote_order_items.order_item_id', '=', 'order_items.id')
            ->join('products', 'order_items.product_id', '=', 'products.id')
            ->join('orders', 'order_items.order_id', '=', 'orders.id')
            ->where(function ($query) use ($filterInputs) {
                if (!is_null($filterInputs)) {
                    if ($filterInputs['start_date'] != '' && $filterInputs['end_date'] != '') {
                        $startDate = Carbon::parse($filterInputs['start_date'] ?? '1970-01-01')->startOfDay();
                        $endDate = Carbon::parse($filterInputs['end_date'] ?? Carbon::now())->endOfDay();

                        $query->whereBetween('tote_order_items.created_at', [$startDate, $endDate]);
                    }
                    if ($filterInputs['sku'] !== '0') {
                        $query->where('products.sku', 'like', $filterInputs['sku'] . '%');
                    }
                    if ($filterInputs['created_date'] !== '0') {
                        $query->where('tote_order_items.created_at', '>=', $filterInputs['created_date']);
                    }
                    if ($filterInputs['order'] !== '0') {
                        $query->where('orders.id', $filterInputs['order']);
                    }
                }
            })
            ->groupBy('tote_order_items.id')
            ->select(
                'tote_order_items.*',
            )
            ->orderBy($sortColumnName, $sortDirection);
    }

    /**
     * @param $filterInputs
     * @param string $sortColumnName
     * @param string $sortDirection
     * @return Tote|Builder
     */
    public function getQuery($filterInputs, string $sortColumnName = 'totes.name', string $sortDirection = 'asc')
    {
        return Tote::query()
            ->join('warehouses', 'totes.warehouse_id', '=', 'warehouses.id')
            ->join('contact_informations AS warehouse_contact_information', 'warehouses.id', '=', 'warehouse_contact_information.object_id')
            ->where('warehouse_contact_information.object_type', Warehouse::class)
            ->select('totes.*')
            ->groupBy('totes.id')
            ->orderBy($sortColumnName, $sortDirection);
    }

    /**
     * @param string $term
     * @param $totesCollection
     * @return mixed
     */
    public function searchQuery(string $term, $totesCollection)
    {
        $term = $term . '%';

        $totesCollection->where(function ($q) use ($term) {
            $q->where('totes.name', 'like', $term)
                ->orWhereHas('warehouse.contactInformation', function ($query) use ($term) {
                    $query->where('name', 'like', $term)
                        ->orWhere('address', 'like', $term)
                        ->orWhere('city', 'like', $term)
                        ->orWhere('zip', 'like', $term)
                        ->orWhere('email', 'like', $term)
                        ->orWhere('phone', 'like', $term);
                });
        });

        return $totesCollection;
    }

    /**
     * @param ImportCsvRequest $request
     * @return array|Application|Translator|string|null
     */
    public function importCsv(ImportCsvRequest $request)
    {
        $input = $request->validated();

        $importLines = app('csv')->getCsvData($input['import_csv']);

        $columns = array_intersect(
            app('csv')->unsetCsvHeader($importLines, 'barcode'),
            TotesExportResource::columns()
        );

        if (!empty($importLines)) {
            $newCollection = new Collection();
            $deletedCollection = new Collection();

            $totesToImport = [];

            foreach ($importLines as $importLine) {
                $data = [];
                $data['customer_id'] = (int)$input['customer_id'];

                foreach ($columns as $columnsIndex => $column) {
                    if (Arr::has($importLine, $columnsIndex)) {
                        $data[$column] = Arr::get($importLine, $columnsIndex);
                    }
                }

                $totesToImport[] = $data;
            }

            $toteToImportIndex = 0;

            foreach ($totesToImport as $toteToImport) {
                $tote = Tote::where('name', $toteToImport['name'])
                    ->whereHas('warehouse', static function($query) use ($toteToImport) {
                        $query->where('customer_id', $toteToImport['customer_id']);
                    })
                    ->first();

                if ($tote) {
                    if (Str::lower($toteToImport['delete']) === 'yes' || Str::lower($toteToImport['delete']) === 'y') {
                        $deletedCollection->add($tote);
                    }
                } else {
                    $newCollection->add($this->store($this->createRequestFromImport($toteToImport), false));
                }

                Session::flash('status', ['type' => 'info', 'message' => __('Importing :current/:total totes', ['current' => ++$toteToImportIndex, 'total' => count($totesToImport)])]);
            }

            if ($newCollection->count() > 0) {
                $this->batchWebhook($newCollection, Tote::class, ToteCollection::class, Webhook::OPERATION_TYPE_STORE);
            }

            if ($deletedCollection->count() > 0) {
                foreach ($deletedCollection as $tote) {
                    if (count($tote->placedToteOrderItems) === 0) {
                        $tote->delete();
                    }
                }
            }
        }

        return __('Totes were successfully imported!');
    }

    /**
     * @param array $data
     * @return StoreRequest|void
     */
    private function createRequestFromImport(array $data)
    {
        $warehouse = Warehouse::where('customer_id', $data['customer_id'])
            ->whereHas('contactInformation', function($query) use ($data) {
                $query->where('name', $data['warehouse']);
            })
            ->first();

        if ($warehouse) {
            $requestData = [
                'customer_id' => $data['customer_id'],
                'warehouse_id' => $warehouse->id,
                'name_prefix' => $data['name'],
                'barcode' => $data['barcode'] ?? $data['name'],
                'number_of_totes' => 1
            ];

            return StoreRequest::make($requestData);
        }
    }

    /**
     * @param ExportCsvRequest $request
     * @return mixed
     */
    public function exportCsv(ExportCsvRequest $request)
    {
        $input = $request->validated();

        $customers = app('user')->getSelectedCustomers();

        $totes = $this->getQuery($request->get('filter_form'));

        $totes = $totes->whereIn('customer_id', $customers->pluck('id')->toArray());

        if (Arr::get($input, 'search')) {
            $totes = $this->searchQuery(Arr::get($input, 'search.value'), $totes);
        }

        $csvFileName = Str::kebab(auth()->user()->contactInformation->name) . '-totes-export.csv';

        return app('csv')->export($request, $totes->get(), TotesExportResource::columns(), $csvFileName, TotesExportResource::class);
    }
}
