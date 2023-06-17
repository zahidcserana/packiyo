<?php

namespace App\Components;

use App\Http\Requests\Csv\{ExportCsvRequest, ImportCsvRequest};
use App\Http\Requests\Order\{BulkSelectionRequest,
    BulkEditRequest,
    DestroyBatchRequest,
    DestroyRequest,
    FilterRequest,
    StoreBatchRequest,
    StoreRequest,
    UpdateBatchRequest,
    UpdateRequest};
use App\Http\Resources\{ExportResources\OrderExportResource, OrderCollection, OrderResource};
use App\Jobs\AllocateInventoryJob;
use App\Jobs\Order\RecalculateReadyToShipOrders;
use App\Models\{Currency,
    Customer,
    CustomerSetting,
    Order,
    OrderItem,
    OrderStatus,
    PrintJob,
    Product,
    ReturnItem,
    Shipment,
    Webhook};
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\{JsonResponse, Request, Resources\Json\ResourceCollection};
use Illuminate\Support\{Arr, Collection, Facades\DB, Facades\Log, Facades\Session, Facades\Storage, Str};
use PDF;
use Symfony\Component\HttpFoundation\StreamedResponse;
use Webpatser\Countries\Countries;

class OrderComponent extends BaseComponent
{
    public function store(StoreRequest $request, $fireWebhook = true)
    {
        $input = $request->validated();
        $input = $this->updateCurrencyInput($input);

        $shippingInformationData = Arr::get($input, 'shipping_contact_information');

        if ($request->get('differentBillingInformation')) {
            $billingInformationData = Arr::get($input, 'billing_contact_information');
        } else {
            $billingInformationData = Arr::get($input, 'shipping_contact_information');
        }

        Arr::forget($input, 'shipping_contact_information');
        Arr::forget($input, 'billing_contact_information');

        if (isset($input['order_status_id']) && $input['order_status_id'] === 'pending') {
            $input['order_status_id'] = null;
        }

        $orderArr = Arr::except($input, ['order_items', 'tags']);

        $order = Order::create($orderArr);
        Order::disableAuditing();
        OrderItem::disableAuditing();

        $shippingContactInformation = $this->createContactInformation($shippingInformationData, $order);
        $billingContactInformation = $this->createContactInformation($billingInformationData, $order);

        $order->shipping_contact_information_id = $shippingContactInformation->id;
        $order->billing_contact_information_id = $billingContactInformation->id;
        $order->save();

        if (!Arr::get($orderArr, 'shipping_method_id') && Arr::get($orderArr, 'shipping_method_name')) {
            app('shippingMethodMapping')->getShippingMethod($order);
        }

        $tags = Arr::get($input, 'tags');
        if (!empty($tags)) {
            $this->updateTags($tags, $order);
        }

        $this->updateOrderItems($order, $input['order_items']);

        $order->getMapCoordinates();

        if (customer_settings($order->customer->id, CustomerSetting::CUSTOMER_SETTING_ORDER_SLIP_AUTO_PRINT)) {
            $defaultSlipPrinter = app('printer')->getDefaultSlipPrinter($order->customer);

            if ($defaultSlipPrinter) {
                PrintJob::create([
                    'object_type' => Order::class,
                    'object_id' => $order->id,
                    'url' => route('order.getOrderSlip', [
                        'order' => $order
                    ]),
                    'printer_id' => $defaultSlipPrinter->id,
                    'user_id' => auth()->user()->id
                ]);
            }
        }

        dispatch(new RecalculateReadyToShipOrders([$order->id]));

        return $order;
    }

    public function storeBatch(StoreBatchRequest $request)
    {
        $responseCollection = new Collection();

        $input = $request->validated();

        foreach ($input as $record) {
            $storeRequest = StoreRequest::make($record);
            $responseCollection->add($this->store($storeRequest, false));
        }

        $this->batchWebhook($responseCollection, Order::class, OrderCollection::class, Webhook::OPERATION_TYPE_STORE);

        return $responseCollection;
    }

    public function update(UpdateRequest $request, Order $order, $fireWebhook = true)
    {
        $input = $request->validated();
        $input = $this->updateCurrencyInput($input);

        if (isset($input['shipping_contact_information'])) {
            $order->shippingContactInformation->update(Arr::get($input, 'shipping_contact_information'));
            Arr::forget($input, 'shipping_contact_information');
        }

        if (isset($input['billing_contact_information'])) {
            $order->billingContactInformation->update(Arr::get($input, 'billing_contact_information'));
            Arr::forget($input, 'billing_contact_information');
        }

        if (isset($input['order_items'])) {
            $this->updateOrderItems($order, Arr::get($input, 'order_items'));
        }

        if (Arr::get($input, 'shipping_method_id') === 'dummy') {
            Arr::set($input, 'shipping_method_id', null);
        }

        if (isset($input['order_status_id']) && $input['order_status_id'] === 'pending') {
            $input['order_status_id'] = null;
        }

        $order->update($input);

        if ($order->wasChanged('allocation_hold')) {
            foreach ($order->orderItems->map->product as $product) {
                AllocateInventoryJob::dispatch($product);
            }
        }

        if (!Arr::get($input, 'shipping_method_id') && Arr::get($input, 'shipping_method_name')) {
            app('shippingMethodMapping')->getShippingMethod($order);
        }

        if (Arr::exists($input, 'tags')) {
            $this->updateTags(Arr::get($input, 'tags'), $order, true);
        }

        if ($fireWebhook) {
            $this->webhook(new OrderResource($order), Order::class, Webhook::OPERATION_TYPE_UPDATE, $order->customer_id);
        }

        $this->updateNotes($order, $request);

        $order->getMapCoordinates();

        if (!empty($order->getDirty())) {
            dispatch(new RecalculateReadyToShipOrders([$order->id]));
        }

        return $order;
    }

    public function updateBatch(UpdateBatchRequest $request)
    {
        $responseCollection = new Collection();

        $input = $request->validated();

        foreach ($input as $record) {
            $updateRequest = UpdateRequest::make($record);
            $order = Order::find($record['id']);

            $responseCollection->add($this->update($updateRequest, $order, false));
        }

        $this->batchWebhook($responseCollection, Order::class, OrderCollection::class, Webhook::OPERATION_TYPE_UPDATE);

        return $responseCollection;
    }

    public function updateOrderItems(Order $order, $orderItems)
    {
        foreach ($orderItems as $item) {
            if (empty($item['product_id']) && !empty($item['sku'])) {
                $product = Product::where('sku', $item['sku'])->where('customer_id', $order['customer_id'])->first();

                if (empty($product)) {
                    continue;
                } else {
                    $item['product_id'] = $product->id;
                }
            }

            if (!isset($item['order_item_id'])) {
                $externalId = Arr::get($item, 'external_id');
                if (!empty($externalId)) {
                    if ($orderItemByExternalId = OrderItem::where('external_id', $externalId)->where('order_id', $order->id)->first()) {
                        $item['order_item_id'] = $orderItemByExternalId->id;
                    }
                }
            }

            if (!isset($item['order_item_id'])) {
                $isKitItem = Arr::get($item, 'is_kit_item');
                if (!empty($item['cancelled']) || ($isKitItem != null && $isKitItem != 'false') || $item['quantity'] == 0) {
                    // do not include cancelled and kit items
                    continue;
                }
                $item['order_id'] = $order->id;
                $orderItem = OrderItem::create($item);
                // If this product is a static kit and it is a top level order item, then create child order items for it
                if ($orderItem->product->kit_type == Product::PRODUCT_TYPE_STATIC_KIT) {
                    foreach ($orderItem->product->kitItems as $kitItem) {
                        $relatedRequestOrderItem = current(array_filter($orderItems, function ($value) use ($kitItem, $orderItem) {
                            return Arr::get($value, 'product_id') == $kitItem->id && Arr::get($value, 'parent_product_id') == $orderItem->product_id;
                        }));

                        $quantity = $kitItem->pivot->quantity * $orderItem->quantity;

                        if ($relatedRequestOrderItem && $relatedRequestOrderItem['cancelled']) {
                            $quantity = 0;
                        }

                        OrderItem::create([
                            'order_id' => $orderItem->order->id,
                            'product_id' => $kitItem->id,
                            'quantity' => $quantity,
                            'order_item_kit_id' => $orderItem->id
                        ]);
                    }
                }
            } else {
                if ((isset($item['cancelled']) && $item['cancelled'] == 1)) {
                    $item['quantity'] = 0;
                }
                $orderItem = OrderItem::findOrFail($item['order_item_id']);
                $orderItem->update($item);
                // If this product is a static kit and it is a top level order item, then create child order items for it
                if ($orderItem->product->kit_type == Product::PRODUCT_TYPE_STATIC_KIT && is_null($orderItem->order_item_kit_id)) {
                    foreach ($orderItem->product->kitItems as $kitItem) {
                        $relatedRequestOrderItem = current(array_filter($orderItems, function ($value) use ($kitItem, $orderItem) {
                            return Arr::get($value, 'product_id') == $kitItem->id && Arr::get($value, 'parent_product_id') == $orderItem->product_id;
                        }));

                        $kitQuantity = $kitItem->pivot->quantity;
                        $quantity =  $kitQuantity * $orderItem->quantity;

                        // TODO: far from ideal, need to figure out what happens if line is partially fulfilled externally
                        if (!empty($relatedRequestOrderItem['cancelled']) || $orderItem->quantity_pending < 1) {
                            $quantity = 0;
                        }

                        $orderItem->kitOrderItems()->updateOrCreate(['product_id' => $kitItem->id], [
                            'order_id' => $orderItem->order->id,
                            'product_id' => $kitItem->id,
                            'quantity' => $quantity,
                            'order_item_kit_id' => $orderItem->id
                        ]);
                    }

                    $componentProductIds = $orderItem->product->kitItems->pluck('pivot.child_product_id');

                    $orderLinesToCancel = $orderItem->kitOrderItems()->whereNotIn('product_id', $componentProductIds)->get();
                    foreach ($orderLinesToCancel as $orderLineToCancel) {
                        $this->cancelOrderItem($orderLineToCancel);
                    }
                }
            }
        }
    }

    public function destroy(DestroyRequest $request, Order $order, $fireWebhook = true): array
    {
        $order->orderItems()->delete();

        $order->delete();

        $response = ['id' => $order->id, 'customer_id' => $order->customer_id];

        if ($fireWebhook) {
            $this->webhook($response, Order::class, Webhook::OPERATION_TYPE_DESTROY, $order->customer_id);
        }

        return $response;
    }

    public function destroyBatch(DestroyBatchRequest $request)
    {
        $responseCollection = new Collection();

        $input = $request->validated();

        foreach ($input as $record) {
            $destroyRequest = DestroyRequest::make($record);
            $order = Order::find($record['id']);

            $responseCollection->add($this->destroy($destroyRequest, $order, false));
        }

        $this->batchWebhook($responseCollection, Order::class, ResourceCollection::class, Webhook::OPERATION_TYPE_DESTROY);

        return $responseCollection;
    }

    public function generateOrderSlip(Order $order)
    {
        $order->refresh();

        $path = 'public/order_slips';
        $pdfName = sprintf("%011d", $order->id) . '_order_slip.pdf';

        if (! Storage::exists($path)) {
            Storage::makeDirectory($path);
        }

        $path .= '/' . $pdfName;

        $paperWidth = dimension_width($order->customer, 'document');
        $paperHeight = dimension_height($order->customer, 'document');

        PDF::loadView('order_slip.document', [
                'order' => $order,
                'showPricesOnSlip' => customer_settings($order->customer->id, CustomerSetting::CUSTOMER_SETTING_SHOW_PRICES_ON_SLIPS),
                'currency' => $order->currency->symbol ?? Currency::find(customer_settings($order->customer->id, CustomerSetting::CUSTOMER_SETTING_CURRENCY))->symbol ?? ''
            ])
            ->setPaper([0, 0, $paperWidth, $paperHeight])
            ->save(Storage::path($path));

        $order->update(['order_slip' => $path]);
    }

    public function getOrderSlip(Order $order)
    {
        $locale = customer_settings($order->customer_id, CustomerSetting::CUSTOMER_SETTING_LOCALE);
        if ($locale) {
            app()->setLocale($locale);
        }

        $this->generateOrderSlip($order);

        return response()->file(Storage::path($order->order_slip), [
            'Content-Type' => 'application/pdf',
        ]);
    }

    public function filterCustomers(Request $request): JsonResponse
    {
        $term = $request->get('term');
        $results = [];

        if ($term) {
            $contactInformation = Customer::whereHas('contactInformation', static function($query) use ($term) {
                $query->where('name', 'like', $term . '%' )
                    ->orWhere('company_name', 'like',$term . '%')
                    ->orWhere('email', 'like',  $term . '%' )
                    ->orWhere('zip', 'like', $term . '%' )
                    ->orWhere('city', 'like', $term . '%' )
                    ->orWhere('phone', 'like', $term . '%' );
            })->get();

            foreach ($contactInformation as $information) {
                $results[] = [
                    'id' => $information->id,
                    'text' => $information->contactInformation->name
                ];
            }
        }

        return response()->json([
            'results' => $results
        ]);
    }

    /**
     * @param Request $request
     * @param Customer|null $customer
     * @return JsonResponse
     */
    public function filterProducts(Request $request, Customer $customer = null): JsonResponse
    {
        $term = $request->get('term');
        $results = [];

        if ($term) {
            $term = $term . '%';

            $products = [];

            if (!is_null($customer)) {

                $products = Product::where('customer_id', $customer->id)
                    ->where(static function ($query) use ($term) {
                        return $query->where('sku', 'like', $term)
                            ->orWhere('name', 'like', $term);
                    })->get();

                foreach ($products as $product) {
                    $childProducts = Product::query()
                        ->with('productImages')
                        ->select('*', 'products.*')
                        ->join('kit_items', 'kit_items.child_product_id', '=', 'products.id')
                        ->whereIn('products.id', DB::table('kit_items')->where('parent_product_id', $product->id)->pluck('child_product_id')->toArray())
                        ->where('kit_items.parent_product_id', $product->id)
                        ->groupBy('products.id')->get();

                    $results[] = [
                        'id' => $product->id,
                        'text' => 'SKU: ' . $product->sku . ', NAME:' . $product->name,
                        'sku' => $product->sku,
                        'name' => $product->name,
                        'image' => $product->productImages[0] ?? null,
                        'price' => $product->price ?? 0,
                        'quantity' => $product->quantity_available ?? 0,
                        'kit_type' => $product->kit_type ?? 0,
                        'child_products' => $childProducts,
                        'default_image' => asset('img/no-image.png'),
                    ];
                }
            }
        }

        return response()->json([
            'results' => $results
        ]);
    }

    /**
     * @param Request $request
     * @param Customer $customer
     * @return JsonResponse
     */
    public function getOrderStatus(Request $request, Customer $customer): JsonResponse
    {
        $results = [];

        $orderStatuses = OrderStatus::where('customer_id', $customer->id)->get();

        foreach ($orderStatuses as $orderStatus) {
            $results[] = [
                'id' => $orderStatus->id,
                'text' => $orderStatus->name
            ];
        }

        return response()->json([
            'results' => $results
        ]);
    }

    /**
     * @param Request $request
     * @return JsonResponse
     */
    public function getBulkOrderStatus(Request $request): JsonResponse
    {
        $fulfilled = true;
        $pending = true;
        $unfulfilled = true;

        $orders = Order::whereIn('id', $request->get('ids'))->get();

        foreach ($orders as $order) {
            if ($order->fulfilled_at) {
                $pending = false;
                $unfulfilled = false;
            } else {
                $fulfilled = false;
            }

            if ($order->cancelled_at) {
                $pending = false;
            }
        }

        return response()->json([
            'results' => compact('pending', 'fulfilled', 'unfulfilled')
        ]);
    }

    public function filter(FilterRequest $request, $customerIds)
    {
        $query = Order::query();

        $query->when($request['from_date_created'], function ($q) use($request){
            return $q->where('created_at', '>=', $request['from_date_created']);
        });

        $query->when($request['to_date_created'], function ($q) use($request){
            return $q->where('created_at', '<=', $request['to_date_created'].' 23:59:59');
        });

        $query->when($request['from_date_updated'], function ($q) use($request){
            return $q->where('updated_at', '>=', $request['from_date_updated']);
        });

        $query->when($request['to_date_updated'], function ($q) use($request){
            return $q->where('updated_at', '<=', $request['to_date_updated'].' 23:59:59');
        });

        $query->when($request['ready_to_ship'], function ($q) use($request){
            return $q->where('ready_to_ship', $request['ready_to_ship']);
        });

        $query->when(count($customerIds) > 0, function ($q) use($customerIds){
            return $q->whereIn('customer_id', $customerIds);
        });

        return $query->paginate();
    }

    public function updatePriorityScore(Order $order)
    {
        if (!$order->fulfilled_at && !$order->cancelled_at) {
            $order->priority_score = now()->diffInDays($order->created_at) * 20 + $order->priority * 10;
        }
    }

    public function search($term, $setDefaultDate = true)
    {
        $customer = app()->user->getSelectedCustomers();

        if (!auth()->user()->isAdmin()) {
            $customers = $customer->pluck('id')->toArray();

            $orderQuery = Order::whereIn('customer_id', $customers);
        } else {
            $customerIds = Auth()->user()->customerIds();
            $orderQuery = Order::whereIn('orders.customer_id', $customerIds);
        }

        return $orderQuery;
    }

    /**
     * @param Order $order
     * @return Order
     */
    public function cancelOrder(Order $order): Order
    {
        if (!$order->fulfilled_at && !$order->cancelled_at) {
            foreach ($order->orderItems as $orderItem) {
                $this->cancelOrderItem($orderItem);
            }

            $order->auditOrderCustomEvent('cancelled');
        }

        return $order;
    }

    /**
     * @param OrderItem $orderItem
     * @return Order
     */
    public function cancelOrderItem(OrderItem $orderItem): Order
    {
        if ($orderItem->quantity_pending > 0) {
            $orderItem->quantity_pending = 0;
            $orderItem->cancelled_at = Carbon::now();

            $orderItem->save();
        }

        foreach ($orderItem->kitOrderItems as $kitOrderItem) {
            if ($kitOrderItem->quantity_pending > 0) {
                $kitOrderItem->quantity_pending = 0;
                $kitOrderItem->cancelled_at = Carbon::now();

                $kitOrderItem->save();
            }
        }

        return $orderItem->order;
    }

    /**
     * @param Order $order
     * @return Order
     */
    public function markAsFulfilled(Order $order): Order
    {
        if (is_null($order->fulfilled_at)) {
            $order->fulfilled_at = Carbon::now();
            $order->saveQuietly();

            foreach ($order->orderItems as $orderItem) {
                $orderItem->quantity_pending = 0;
                $orderItem->saveQuietly();

                AllocateInventoryJob::dispatch($orderItem->product);
            }

            $order->auditOrderCustomEvent('fulfilled');
        }

        return $order;
    }

    /**
     * @param Order $order
     * @return bool
     */
    public function isOrderPartiallyShipped(Order $order): bool
    {
        foreach ($order->orderItems as $orderItem) {
            if ($orderItem->quantity_shipped > 0) {
                return true;
            }
        }

        return false;
    }

    /**
     * @param Order $order
     * @return void
     */
    public function recalculateStatus(Order $order): void
    {
        $fulfilled = true;
        $hasQuantityPending = false;
        $hasQuantityShipped = false;

        if (!is_null($order->cancelled_at)) {
            $fulfilled = false;
        }

        foreach ($order->orderItems as $orderItem) {
            if ($orderItem->quantity_pending > 0) {
                $hasQuantityPending = true;
            }

            if ($orderItem->quantity_shipped > 0) {
                $hasQuantityShipped = true;
            }
        }

        if (!$hasQuantityPending && $hasQuantityShipped && $fulfilled && count($order->orderItems) > 0) {
            $order->fulfilled_at = Carbon::now();
        } else {
            $order->fulfilled_at = null;
        }

        if (!$hasQuantityPending && !$hasQuantityShipped && is_null($order->fulfilled_at) && count($order->orderItems) > 0) {
            $order->cancelled_at = Carbon::now();
        } else {
            $order->cancelled_at = null;
        }

        if (is_null($order->ordered_at)) {
            $order->ordered_at = Carbon::now();
        }
    }

    /**
     * @param Order $order
     * @return void
     */
    public function recalculateTotals(Order $order): void
    {
        $subtotal = 0;

        foreach ($order->orderItems as $orderItem) {
            $price = (float)$orderItem->price;
            $subtotal += $price;
        }

        $order->subtotal = $subtotal;

        $order->total = $order->subtotal + $order->tax + $order->shipping;
    }

    /**
     * @param int[]|null $orderIds
     * @return void
     * @throws \Exception
     */
    public function recalculateReadyToShipOrders(array $orderIds = null): void
    {
        if (!empty($orderIds)) {
            $condition = 'id IN (' . implode(',', $orderIds) . ')';
        } else {
            $condition = '(fulfilled_at IS NULL AND cancelled_at IS NULL) OR ready_to_ship = 1 OR ready_to_pick = 1';
        }

        DB::transaction(function() use ($condition) {
            DB::update(
                'UPDATE orders SET
	                    ready_to_pick = IF(
	                        (required_shipping_date_at IS NULL OR required_shipping_date_at < NOW()) AND
                            address_hold = 0 AND
                            fraud_hold = 0 AND
                            operator_hold = 0 AND
                            payment_hold = 0 AND
                            fulfilled_at IS NULL AND
                            cancelled_at IS NULL AND
                            quantity_allocated_pickable_sum > 0 AND
                            (
                                quantity_allocated_pickable_sum = quantity_pending_sum OR
                                allow_partial = 1
                            ),
                        1,
                        0
                        ),
                        ready_to_ship = IF(
                            (required_shipping_date_at IS NULL OR required_shipping_date_at < NOW()) AND
                            address_hold = 0 AND
                            fraud_hold = 0 AND
                            operator_hold = 0 AND
                            payment_hold = 0 AND
                            fulfilled_at IS NULL AND
                            cancelled_at IS NULL AND
                            quantity_allocated_sum > 0 AND
                            (
                                quantity_allocated_sum = quantity_pending_sum OR
                                allow_partial = 1
                            ),
                        1,
                        0
                    ) WHERE ' . $condition
            );
        }, 10);
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
            app('csv')->unsetCsvHeader($importLines, 'shipping_contact_information_name'),
            OrderExportResource::columns()
        );

        if (!empty($importLines)) {
            $storedCollection = new Collection();
            $updatedCollection = new Collection();

            $ordersToImport = [];

            foreach ($importLines as $importLine) {
                $data = [];
                $data['customer_id'] = $input['customer_id'];

                foreach ($columns as $columnsIndex => $column) {
                    if (Arr::has($importLine, $columnsIndex)) {
                        $data[$column] = Arr::get($importLine, $columnsIndex);
                    }
                }

                if (!Arr::has($ordersToImport, $data['order_number'])) {
                    $ordersToImport[$data['order_number']] = [];
                }

                $ordersToImport[$data['order_number']][] = $data;
            }

            $orderToImportIndex = 0;

            foreach ($ordersToImport as $number => $orderToImport) {
                $order = Order::where('customer_id', $orderToImport[0]['customer_id'])->where('number', $number)->first();

                DB::transaction(function() use ($orderToImport, $storedCollection, $updatedCollection, $order) {
                    if ($order) {
                        $updatedCollection->add($this->update($this->createRequestFromImport($orderToImport, $order, true), $order,false));
                    } else {
                        $storedCollection->add($this->store($this->createRequestFromImport($orderToImport), $order));
                    }
                }, 10);

                Session::flash('status', ['type' => 'info', 'message' => __('Importing :current/:total orders', ['current' => ++$orderToImportIndex , 'total' => count($ordersToImport)])]);
                Session::save();
            }

            $this->batchWebhook($storedCollection, Order::class, OrderCollection::class, Webhook::OPERATION_TYPE_STORE);
            $this->batchWebhook($updatedCollection, Order::class, OrderCollection::class, Webhook::OPERATION_TYPE_UPDATE);
        }

        Session::flash('status', ['type' => 'success', 'message' => __('Orders were successfully imported!')]);

        return __('Orders were successfully imported!');
    }

    /**
     * @param ExportCsvRequest $request
     * @return StreamedResponse
     */
    public function exportCsv(ExportCsvRequest $request): StreamedResponse
    {
        $input = $request->validated();
        $search = $input['search']['value'];

        if ($search) {
            $orders = $this->getQuery();

            $orders = $this->searchQuery($search, $orders);
        } else {
            $orders = $this->getQuery($request->get('filter_form'));
        }

        $csvFileName = Str::kebab(auth()->user()->contactInformation->name) . '-orders-export.csv';

        return app('csv')->export($request, $orders->get(), OrderExportResource::columns(), $csvFileName, OrderExportResource::class);
    }

    /**
     * @param array $data
     * @param Order|null $order
     * @param bool $update
     * @return StoreRequest|UpdateRequest
     */
    private function createRequestFromImport(array $data, Order $order = null, bool $update = false)
    {
        $orderStatus = OrderStatus::where('customer_id', Arr::get($data[0], 'customer_id'))->where('name', Arr::get($data[0], 'status'))->first();

        $shippingCountry = Countries::find(Arr::get($data[0], 'shipping_contact_information_country'));

        if (!$shippingCountry) {
            $shippingCountry = Countries::where('iso_3166_2', Arr::get($data[0], 'shipping_contact_information_country'))->first();
        }

        $requestData = [
            'customer_id' => Arr::get($data[0], 'customer_id'),
            'number' => Arr::get($data[0], 'order_number'),
            'order_status_id' => $orderStatus->id ?? 'pending',
            'shipping_method_name' => Arr::get($data[0], 'shipping_method_name', 'dummy'),
            'shipping_contact_information' => [
                'name' => Arr::get($data[0], 'shipping_contact_information_name'),
                'company_name' => Arr::get($data[0], 'shipping_contact_information_company_name'),
                'company_number' => Arr::get($data[0], 'shipping_contact_information_company_number'),
                'address' => Arr::get($data[0], 'shipping_contact_information_address'),
                'address2' => Arr::get($data[0], 'shipping_contact_information_address2'),
                'zip' => Arr::get($data[0], 'shipping_contact_information_zip'),
                'city' => Arr::get($data[0], 'shipping_contact_information_city'),
                'state' => Arr::get($data[0], 'shipping_contact_information_state'),
                'country_id' => $shippingCountry->id,
                'phone' => Arr::get($data[0], 'shipping_contact_information_phone'),
                'email' => Arr::get($data[0], 'shipping_contact_information_email')
            ],
            'tags' => explode(',', Arr::get($data[0], 'tags'))
        ];

        $billingCountry = Countries::find(Arr::get($data[0], 'billing_contact_information_country'));

        if (!$billingCountry) {
            $billingCountry = Countries::where('iso_3166_2', Arr::get($data[0], 'billing_contact_information_country'))->first();
        }

        if ($billingCountry) {
            $requestData['billing_contact_information'] = [
                'name' => Arr::get($data[0], 'billing_contact_information_name'),
                'company_name' => Arr::get($data[0], 'billing_contact_information_company_name'),
                'company_number' => Arr::get($data[0], 'billing_contact_information_company_number'),
                'address' => Arr::get($data[0], 'billing_contact_information_address'),
                'address2' => Arr::get($data[0], 'billing_contact_information_address2'),
                'zip' => Arr::get($data[0], 'billing_contact_information_zip'),
                'city' => Arr::get($data[0], 'billing_contact_information_city'),
                'state' => Arr::get($data[0], 'billing_contact_information_state'),
                'country_id' => $billingCountry->id,
                'phone' => Arr::get($data[0], 'billing_contact_information_phone'),
                'email' => Arr::get($data[0], 'billing_contact_information_email')
            ];
        } else {
            $requestData['billing_contact_information'] = $requestData['shipping_contact_information'];
        }

        foreach ($data as $key => $line) {
            $product = Product::where('customer_id', $data[0]['customer_id'])->where('sku', trim($line['sku']))->first();

            $requestData['order_items'][$key] = [
                'product_id' => $product->id ?? null,
                'quantity' => $line['quantity'],
            ];

            if ($update) {
                $orderItem = OrderItem::where('order_id', $order->id)->where('product_id', $requestData['order_items'][$key]['product_id'])->first();

                if (!is_null($orderItem)) {
                    $requestData['order_items'][$key]['order_item_id'] = $orderItem->id;
                }
            }
        }

        return $update ? UpdateRequest::make($requestData) : StoreRequest::make($requestData);
    }

    /**
     * @param $customers
     * @param array $filterInputs
     * @return Builder
     */
    public function getQuery(array $filterInputs = []): Builder
    {
        $customerIds = app('user')->getSelectedCustomers()->pluck('id')->toArray();

        $filterCustomerId = Arr::get($filterInputs, 'customer_id');

        if ($filterCustomerId && $filterCustomerId != 'all') {
            $customerIds = array_intersect($customerIds, [$filterCustomerId]);
        }

        $orderCollection = Order::with([
            'customer.contactInformation',
            'shippingContactInformation.country',
            'orderStatus',
            'orderItems.placedToteOrderItems.tote',
            'tags'
        ])
            ->whereIn('orders.customer_id', $customerIds)
            ->where(function ($query) use ($filterInputs) {
                // Find by filter result
                // Start/End date
                if (Arr::get($filterInputs, 'start_date') || Arr::get($filterInputs, 'end_date')) {
                    $startDate = Carbon::parse($filterInputs['start_date'] ?? '1970-01-01')->startOfDay();
                    $endDate = Carbon::parse($filterInputs['end_date'] ?? Carbon::now())->endOfDay();

                    $query->whereBetween('orders.ordered_at', [$startDate, $endDate]);
                }

                // Order Status
                if (Arr::get($filterInputs, 'order_status')) {
                    if ($filterInputs['order_status'] === 'fulfilled') {
                        $query->whereNotNull('orders.fulfilled_at');
                    } else if ($filterInputs['order_status'] === 'cancelled') {
                        $query->whereNotNull('orders.cancelled_at');
                    } else if ($filterInputs['order_status'] === 'pending') {
                        $query
                            ->whereNull([
                                'orders.cancelled_at',
                                'orders.fulfilled_at',
                                'orders.order_status_id'
                            ]);
                    } else {
                        $query
                            ->where('orders.order_status_id', (int)$filterInputs['order_status'])
                            ->whereNull([
                                'orders.cancelled_at',
                                'orders.fulfilled_at'
                            ]);
                    }
                }

                if (isset($filterInputs['in_tote'])) {
                    if ($filterInputs['in_tote'] == 1) {
                        $query->has('orderItems.placedToteOrderItems');
                    } else {
                        $query->doesntHave('orderItems.placedToteOrderItems');
                    }
                }

                // Ready To Ship
                if (($filterInputs['ready_to_ship'] ?? 'all') !== 'all') {
                    $query->where('orders.ready_to_ship', (int) $filterInputs['ready_to_ship']);
                }

                // Priority
                if (Arr::get($filterInputs, 'priority_to') || Arr::get($filterInputs, 'priority_from')) {
                    $from = $filterInputs['priority_from'] ?? 0;
                    $to = $filterInputs['priority_to'] ?? Order::query()->max('priority');

                    $query->whereBetween('orders.priority', [(int)$from, (int)$to]);
                }

                // Backorder
                if (Arr::get($filterInputs, 'backordered')) {
                    if ((int)$filterInputs['backordered'] === 0) {
                        $query->whereHas('orderItems', function ($q) {
                            return $q->where('quantity_backordered', '>', 0);
                        });
                    } else if ((int)$filterInputs['backordered'] === 1) {
                        $query->whereDoesntHave('orderItems', function ($q) {
                            return $q->where('quantity_backordered', '>', 0);
                        });
                    }
                }

                // Weight
                if (Arr::get($filterInputs, 'weight_to') || Arr::get($filterInputs, 'weight_from')) {
                    $from = $filterInputs['weight_from'] ?? 0;
                    $to = $filterInputs['weight_to'] ?? Order::query()->max('weight');

                    $query->whereBetween('orders.weight', [(int)$from, (int)$to]);
                }

                // Any hold
                if (Arr::exists($filterInputs, 'any_hold') && $filterInputs['any_hold'] !== null) {
                    match ($filterInputs['any_hold']) {
                        'any_hold' => $query->where(function($q) {
                            $q->where('fraud_hold', 1)
                                ->orWhere('address_hold', 1)
                                ->orWhere('payment_hold', 1)
                                ->orWhere('operator_hold', 1)
                                ->orWhere('allocation_hold', 1)
                                ->orWhere('required_shipping_date_at', '>', now()->toDateString());
                        }),
                        'fraud_hold' => $query->where('fraud_hold', 1),
                        'address_hold' => $query->where('address_hold', 1),
                        'payment_hold' => $query->where('payment_hold', 1),
                        'operator_hold' => $query->where('operator_hold', 1),
                        'allocation_hold' => $query->where('allocation_hold', 1),
                        'hold_until' => $query->where('required_shipping_date_at', '>', now()->toDateString()),
                        'none' => $query->where(function($q) {
                            $q->where('fraud_hold', 0)
                                ->where('address_hold', 0)
                                ->where('payment_hold', 0)
                                ->where('operator_hold', 0)
                                ->where('allocation_hold', 0)
                                ->where(function($q) {
                                    $q->whereDate('required_shipping_date_at', '<=', now()->toDateString())
                                    ->orWhereNull('required_shipping_date_at');
                                });
                        }),
                        default => $query,
                    };
                }

                // Tags
                if (Arr::exists($filterInputs, 'tags') && Arr::get($filterInputs, 'tags')) {
                    $filterTags = (array) $filterInputs['tags'];
                    $query->whereHas('tags', function($query) use ($filterTags) {
                        $query->whereIn('name', $filterTags);
                    });
                }

                // Required ship date
                if (Arr::get($filterInputs, 'shipping_date_before_at')) {
                    $query->where('shipping_date_before_at', Carbon::parse($filterInputs['shipping_date_before_at'])->toDateString());
                }
            })
            ->select('orders.*');

        // Country
        if (Arr::get($filterInputs, 'country')) {
            $orderCollection->join('contact_informations', 'contact_informations.id', '=', 'orders.shipping_contact_information_id');
            $orderCollection->where('contact_informations.country_id', $filterInputs['country']);
        }

        // Shipping Method
        if (Arr::get($filterInputs, 'shipping_method')) {
            $orderCollection->join('shipping_methods', 'shipping_methods.id', '=', 'orders.shipping_method_id');
            $orderCollection->where('shipping_methods.name', $filterInputs['shipping_method']);
        }

        // Carriers
        if (Arr::get($filterInputs, 'shipping_carrier')) {
            if (!collect($orderCollection->getQuery()->joins)->pluck('table')->contains('shipping_methods')) {
                $orderCollection->join('shipping_methods', 'shipping_methods.id', '=', 'orders.shipping_method_id');
            }

            $orderCollection->join('shipping_carriers', 'shipping_carriers.id', '=', 'shipping_methods.shipping_carrier_id');
            $orderCollection->where('shipping_carriers.name', $filterInputs['shipping_carrier']);
        }

        return $orderCollection;
    }


    /**
     * @param string $search
     * @param $orders
     * @return mixed
     */
    public function searchQuery(string $search, $orders)
    {
        $term = $search . '%';

        $orders->where('number', 'like', $term);

        return $orders;
    }

    public function setOperatorHold(Order $order)
    {
        $order->update(['operator_hold' => 1]);
    }

    public function updateOrderStatus(Order $order, $orderStatusId)
    {
        $order->update(['order_status_id' => $orderStatusId]);
    }

    public function reshipOrderItems(Order $order, $request)
    {
        $reshippedItemsNum = 0;

        foreach ($request->order_items as $data) {
            if (isset($data['order_item_id'])) {
                $reshippedItemsNum ++;
                $orderItemId = $data['order_item_id'];
                $quantityReship = $data['quantity'];

                $orderItem = OrderItem::findOrFail($orderItemId);

                $this->reshipItem($orderItem, $quantityReship, isset($data['add_inventory']));
            }
        }

        if ($reshippedItemsNum > 0) {
            if ($request->operator_hold) {
                app('order')->setOperatorHold($order);
            }
            if ($request->reship_order_status_id > 0) {
                app('order')->updateOrderStatus($order, $request->reship_order_status_id);
            }

            $order->update();

            $order->auditOrderCustomEvent('reshipped');
        }

        return $reshippedItemsNum;
    }

    public function reshipItem(OrderItem $orderItem, $reshipQuantity, $addInventory)
    {
        $orderItem->increment('quantity_reshipped', $reshipQuantity);
        $orderItem->increment('quantity_pending', $reshipQuantity);

        if ($addInventory && $orderItem->packageOrderItems->last()->location) {
            $warehouse = $orderItem->packageOrderItems->last()->location->warehouse;
            if ($warehouse) {
                $location = $warehouse->reshipLocation();
                if ($location) {
                    app('inventoryLog')->adjustInventory($location, $orderItem->product, $reshipQuantity, InventoryLogComponent::OPERATION_TYPE_RESHIP);
                }
            }
        }
    }

    public function voidShipment(Shipment $shipment) {
        foreach ($shipment->shipmentItems as $shipmentItem) {
            $this->reshipItem($shipmentItem->orderItem, $shipmentItem->quantity, true);
        }
    }

    public function getShippedOrderItems(Order $order)
    {
        $orderId = $order->id;

        $orderItems = OrderItem::where('order_id', $orderId)
            ->where('quantity_shipped', '>', 0)
            ->whereHas('product', function ($query) {
                $query->where('kit_type', Product::PRODUCT_TYPE_REGULAR);
            })
            ->get();

        if ($orderId) {
            $orderReturnsIds = Order::find($orderId)
                ->returns()
                // TODO Check if condition should be added
                // ->where('approved', true)
                ->pluck('id')
                ->toArray();

            $returnItems = ReturnItem::whereIn('return_id', $orderReturnsIds)
                ->whereIn('product_id', $orderItems->pluck('product_id')->toArray())
                ->get()
                ->groupBy('product_id')
                ->map(function ($items) {
                    return $items->sum('quantity');
                })
                ->toArray();
        }

        $results = [];

        foreach ($orderItems as $orderItem) {
            $quantity = $orderItem->quantity_shipped;

            if (isset($returnItems[$orderItem->product_id])) {
                $quantity -= $returnItems[$orderItem->product_id];
            }

            if ((int)$quantity > 0) {
                $results[] = [
                    'id' => $orderItem->product_id,
                    'location_id' => $orderItem->product->locations->first() ? $orderItem->product->locations->first()->id : '',
                    'order_item_id' => $orderItem->id,
                    'image' => $orderItem->product->productImages->first()->source ?? asset('img/inventory.svg'),
                    'text' => 'NAME: ' . $orderItem->product->name . '<br>SKU: ' . $orderItem->product->sku,
                    'quantity' => $quantity,
                ];
            }
        }

        return $results;
    }

    /**
     * @param BulkEditRequest $request
     * @return void
     */
    public function bulkEdit(BulkEditRequest $request): void
    {
        $input = $request->validated();
        $orderIds = explode(',', Arr::get($input, 'ids'));
        $updateColumns = [];

        if (!is_null($addTags = Arr::get($input, 'add_tags'))) {
            $this->bulkUpdateTags($addTags, $orderIds, Order::class);
        }

        if (!is_null($removeTags = Arr::get($input, 'remove_tags'))) {
            $this->bulkRemoveTags($removeTags, $orderIds);
        }

        if (!is_null($countryId = Arr::get($input, 'country_id'))) {
            $updateColumns['shipping_contact_information'] = [
                'country_id' => $countryId
            ];
        }

        if (Arr::get($input, 'allow_partial') !== '0') {
            $updateColumns['allow_partial'] = true;
        }

        if (Arr::get($input, 'priority') !== '0') {
            $updateColumns['priority'] = 1;
        }

        if (!is_null($shippingMethodId = Arr::get($input, 'shipping_method_id'))) {
            $updateColumns['shipping_method_id'] = $shippingMethodId;
        }

        foreach ($input as $key => $value) {
            if (!is_null($value) && str_contains($key, '_note')) {
                $updateColumns['append_' . $key] = $value;
            }
        }

        $updateColumns = array_merge($updateColumns, $this->bulkManageHolds($input));

        $updateBatchRequest = [];

        foreach ($orderIds as $orderId) {
            $updateBatchRequest[] = ['id' => $orderId] + $updateColumns;
        }

        $this->updateBatch(UpdateBatchRequest::make($updateBatchRequest));
    }

    /**
     * @param BulkSelectionRequest $request
     * @return void
     */
    public function bulkCancel(BulkSelectionRequest $request): void
    {
        $input = $request->validated();

        $orderIds = explode(',', Arr::get($input, 'ids'));

        foreach ($orderIds as $orderId) {
            $this->cancelOrder(Order::find($orderId));
        }
    }

    /**
     * @param BulkSelectionRequest $request
     * @return void
     */
    public function bulkFulfill(BulkSelectionRequest $request): void
    {
        $input = $request->validated();

        $orderIds = explode(',', Arr::get($input, 'ids'));

        foreach ($orderIds as $orderId) {
            $this->markAsFulfilled(Order::find($orderId));
        }
    }

    /**
     * @param array $input
     * @return array
     */
    private function bulkManageHolds(array $input): array
    {
        if (Arr::get($input, 'remove_all_holds') === '1') {
            return [
                'payment_hold' => 0,
                'fraud_hold' => 0,
                'address_hold' => 0,
                'operator_hold' => 0,
                'allocation_hold' => 0
            ];
        }

        $holdTypes = [
            'payment_hold',
            'fraud_hold',
            'address_hold',
            'operator_hold',
            'allocation_hold'
        ];

        $holds = [];

        foreach ($holdTypes as $hold) {
            if (Arr::get($input, 'remove_' . $hold) === '1') {
                $holds[$hold] = 0;
            } elseif (Arr::get($input, 'add_' . $hold) === '1') {
                $holds[$hold] = 1;
            }
        }

        return $holds;
    }

    /**
     * @param Order $order
     * @param $request
     * @return void
     */
    protected function updateNotes(Order $order, $request): void
    {
        if(isset($request['note_type_append']) && $request['note_text_append'] !== '') {
            $order->refresh();

            $order[$request['note_type_append']] .= empty($order[$request['note_type_append']]) ? $request['note_text_append'] : ' ' . $request['note_text_append'];
            $order->update([$request['note_type_append'] => $order[$request['note_type_append']]]);
        } else {
            if (isset($request['append_packing_note'])) {
                $order->packing_note .= empty($order->packing_note) ? $request['append_packing_note'] : ' ' . $request['append_packing_note'];
                $order->update(['packing_note' => $order->packing_note]);
                Arr::forget($request, 'append_packing_note');
            }

            if (isset($request['append_slip_note'])) {
                $order->slip_note .= empty($order->slip_note) ? $request['append_slip_note'] : ' ' . $request['append_slip_note'];
                $order->update(['slip_note' => $order->slip_note]);
                Arr::forget($request, 'append_slip_note');
            }

            if (isset($request['append_gift_note'])) {
                $order->gift_note .= empty($order->gift_note) ? $request['append_gift_note'] : ' ' . $request['append_gift_note'];
                $order->update(['gift_note' => $order->gift_note]);
                Arr::forget($request, 'append_gift_note');
            }

            if (isset($request['append_internal_note'])) {
                $order->internal_note .= empty($order->internal_note) ? $request['append_internal_note'] : ' ' . $request['append_internal_note'];
                $order->update(['internal_note' => $order->internal_note]);
                Arr::forget($request, 'append_internal_note');
            }
        }
    }

    public function updateSummedQuantities($orderIds = [])
    {
        if (!empty($orderIds)) {
            DB::transaction(function() use ($orderIds) {
                DB::update(
                    'UPDATE
                            `orders`
                        LEFT JOIN(SELECT
                                `order_id`,
                                SUM(`quantity_pending`) AS `order_items_quantity_pending_sum`,
                                SUM(`quantity_allocated`) AS `order_items_quantity_allocated_sum`,
                                SUM(`quantity_allocated_pickable`) AS `order_items_quantity_allocated_pickable_sum`
                            FROM
                                `order_items`
                            WHERE
                                `order_id` IN(' . implode(',', $orderIds) . ') AND `id` NOT IN (SELECT `order_item_kit_id` FROM `order_items` WHERE `order_id` IN (' . implode(',', $orderIds) . ') AND `order_item_kit_id` IS NOT NULL)
                            GROUP BY
                                `order_id`) `summed_order_items`
                        ON
                            `orders`.`id` = `summed_order_items`.`order_id`
                        SET
                            `quantity_pending_sum` = IFNULL(`order_items_quantity_pending_sum`, 0),
                            `quantity_allocated_sum` = IFNULL(`order_items_quantity_allocated_sum`, 0),
                            `quantity_allocated_pickable_sum` = IFNULL(`order_items_quantity_allocated_pickable_sum`, 0)
                        WHERE
                            `orders`.`id` IN(' . implode(',', $orderIds) . ');'
                );
            }, 10);
        }
    }

    public function updateSummedQuantitiesV2($orderIds = [])
    {
        foreach ($orderIds as $orderId) {
            $orderItems = OrderItem::select('order_items.*')
                ->join('products', 'products.id', '=', 'order_items.product_id')
                ->where('order_id', $orderId)
                ->where('is_kit', 0)
                ->get();

            Order::where('id', $orderId)
                ->update([
                    'quantity_pending_sum' => $orderItems->sum('quantity_pending'),
                    'quantity_allocated_sum' => $orderItems->sum('quantity_allocated'),
                    'quantity_allocated_pickable_sum' => $orderItems->sum('quantity_allocated_pickable'),
                ]);
        }
    }

    /**
     * @param array $tags
     * @param array $ids
     * @return void
     */
    private function bulkRemoveTags(array $tags, array $ids): void
    {
        try {
            foreach($ids as $id) {
                $order = Order::find($id);

                foreach ($tags as $tag) {
                    $order->tags()->where('name', 'LIKE', $tag)->delete();
                }

                $order->auditTagCustomEvent($tags);
            }
        } catch (\Exception $exception) {
            Log::error($exception->getMessage());
        }
    }
}
