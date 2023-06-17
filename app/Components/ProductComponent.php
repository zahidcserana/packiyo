<?php

namespace App\Components;

use App\Http\Requests\BulkSelectionRequest;
use App\Http\Requests\Csv\{ExportCsvRequest, ImportCsvRequest};
use App\Http\Requests\Product\{BulkEditRequest,
    ChangeLocationQuantityRequest,
    DestroyBatchRequest,
    DestroyRequest,
    FilterRequest,
    StoreBatchRequest,
    StoreRequest,
    UpdateBatchRequest,
    UpdateRequest};
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use App\Http\Resources\{ExportResources\ProductExportResource, ProductCollection, ProductResource};
use App\Models\{Customer, Image, Location, LocationProduct, LotItem, Order, PrintJob, Product, Supplier, Webhook};
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\{JsonResponse, Request, Resources\Json\ResourceCollection};
use Illuminate\Support\{Arr, Collection, Facades\DB, Facades\Log, Facades\Session, Facades\Storage, Str};
use PDF;
use Picqer\Barcode\BarcodeGeneratorPNG;
use Symfony\Component\HttpFoundation\StreamedResponse;
use Venturecraft\Revisionable\Revision;
use Webpatser\Countries\Countries;

class ProductComponent extends BaseComponent
{
    public function __construct()
    {

    }

    public function store(StoreRequest $request, $fireWebhook = true)
    {
        $input = $request->validated();

        if (!Arr::get($input, 'country_of_origin')) {
            $country = Countries::where('iso_3166_2', Arr::get($input, 'country_of_origin_code'))->first();

            if ($country) {
                Arr::set($input, 'country_of_origin', $country->id);
            }
        }

        if (empty($input['price'])) Arr::set($input, 'price', 0);

        $product = Product::create($input);
        Image::disableAuditing();
        $customer = Customer::find($input['customer_id']);

        if (!empty($input['suppliers']) && count($input['suppliers'])) {
            $product->suppliers()->sync($input['suppliers']);
        }

        if (isset($input['product_images'])) {
            $this->updateProductImageURLs($product, $input['product_images']);
        }

        if ($images = $request->file('file')) {
            $this->updateProductImages($product, $images, $customer);
        }

        if (! empty($input['is_kit']) && (($input['is_kit'] == '1' || $input['is_kit'] == '2')) && ! empty($input['kit_items']) && count($input['kit_items'])) {
            $syncItems = [];

            foreach ($input['kit_items'] as $item) {
                $syncItems[$item['id']] = ['quantity' => $item['quantity']];
            }

            $product->kitItems()->sync($syncItems, false);

            $product->kit_type = $input['is_kit'];
            $product->is_kit = 1;

            $product->save();
        }

        $tags = Arr::get($input, 'tags');
        if (!empty($tags)) {
            $this->updateTags($tags, $product);
        }

        if ($fireWebhook) {
            $this->webhook(new ProductResource($product), Product::class, Webhook::OPERATION_TYPE_STORE, $product->customer_id);
        }

        return $product;
    }

    public function storeBatch(StoreBatchRequest $request)
    {
        $responseCollection = new Collection();

        $input = $request->validated();

        foreach ($input as $record) {
            $storeRequest = StoreRequest::make($record);
            $responseCollection->add($this->store($storeRequest, false));
        }

        $this->batchWebhook($responseCollection, Product::class, ProductCollection::class, Webhook::OPERATION_TYPE_STORE);

        return $responseCollection;
    }

    public function update(UpdateRequest $request, Product $product = null, $fireWebhook = true)
    {
        $input = $request->validated();

        if (!Arr::get($input, 'country_of_origin')) {
            $country = Countries::where('iso_3166_2', Arr::get($input, 'country_of_origin_code'))->first();

            if ($country) {
                Arr::set($input, 'country_of_origin', $country->id);
            }
        }

        if (isset($input['kit_type'])) {
            if ($input['kit_type'] == Product::PRODUCT_TYPE_STATIC_KIT) {
                $product->kit_type = Product::PRODUCT_TYPE_STATIC_KIT;
                $product->is_kit = 1;
            } else if ($input['kit_type'] == Product::PRODUCT_TYPE_REGULAR) {
                $product->kit_type = Product::PRODUCT_TYPE_REGULAR;
                $product->is_kit = 0;

                DB::table('kit_items')
                    ->where('parent_product_id', $product->id)
                    ->delete();
            }
        }

        if (is_null($product)) {
            $product = Product::where('customer_id', Arr::get($input, 'customer_id'))->where('sku', Arr::get($input, 'sku'))->first();
        }

        if (isset($input['priority_counting_requested_at']) && $input['priority_counting_requested_at'] == '1') {
            $input['priority_counting_requested_at'] = now();
        } else {
            $input['priority_counting_requested_at'] = null;
        }

        $product->update($input);

        if (!empty($input['update_vendor'])) {
            if (empty($input['suppliers']) || !count($input['suppliers'])) {
                $input['suppliers'] = [];
            }
            $product->suppliers()->sync($input['suppliers']);
        }

        if (isset($input['product_images'])) {
            $this->updateProductImageURLs($product, $input['product_images']);
        }

        if ($images = $request->file('file')) {
            $this->updateProductImages($product, $images, $product->customer);
        }

        if (!empty($input['update_kit']) || !empty($input['kit_items'])) {
            if ((!empty($input['is_kit']) && $input['is_kit'] === '1') || (!empty($input['kit_items']) && count($input['kit_items']))) {
                $syncItems = [];

                foreach ($input['kit_items'] as $item) {
                    if (isset($item['id']) && ! is_null($item['quantity'])) {
                        $syncItems[$item['id']] = ['quantity' => $item['quantity']];
                    }
                }

                $oldKitItems = $product->kitItems()->get();

                $product->kitItems()->sync($syncItems);

                $newKitItems = $product->kitItems()->get();

                $product->auditKitItems($oldKitItems, $newKitItems);
            } else {
                $product->kitItems()->detach();

                $product->save();
            }
        }

        if (isset($input['product_locations']) && !empty($input['product_locations'])) {

            $detachableLocations = $product->locations->pluck('id');

            if (!empty($input['product_locations']) && count($input['product_locations'])) {
                foreach ($input['product_locations'] as $key => $location) {
                    if (!is_null($location)) {

                        if (!empty($input['product_lots'][$key])) {

                            $lotId = $input['product_lots'][$key]['id'];
                            if ($lotId > 0) {

                                $lotItem = LotItem::where('location_id', $location['id'])
                                    ->whereHas('lot', function($q) use ($product){
                                        $q->where('product_id', $product->id);
                                    })
                                    ->first();

                                if (empty($lotItem)) {
                                    $lotItem = new LotItem();
                                }

                                $lotItem->lot_id = $lotId;
                                $lotItem->location_id = $location['id'];
                                $lotItem->quantity_added = $location['quantity'];
                                $lotItem->save();
                            }
                        }

                        $this->addToLocation($product, $location['id'], $location['quantity']);

                        $detachableLocations = $detachableLocations->filter(function($item) use($location) {
                            return $item != $location['id'];
                        });
                    }
                }
            }
            $detachableLocations = $detachableLocations->toArray();

            if (!empty($detachableLocations) && count($detachableLocations)) {
                foreach ($detachableLocations as $location) {
                    $this->removeFromLocation($product, $location);
                }
            }
        }

        if (Arr::exists($input, 'tags')) {
            $this->updateTags(Arr::get($input, 'tags'), $product, true);
        }

        if ($fireWebhook) {
            $this->webhook(new ProductResource($product), Product::class, Webhook::OPERATION_TYPE_UPDATE, $product->customer_id);
        }

        return $product;
    }

    public function updateBatch(UpdateBatchRequest $request)
    {
        $responseCollection = new Collection();

        $input = $request->validated();

        foreach ($input as $record) {
            $updateRequest = UpdateRequest::make($record);

            if (isset($record['id'])) {
                $responseCollection->add($this->update($updateRequest, Product::find($record['id']), false));
            } else {
                $responseCollection->add($this->update($updateRequest, null, false));
            }
        }

        $this->batchWebhook($responseCollection, Product::class, ProductCollection::class, Webhook::OPERATION_TYPE_UPDATE);

        return $responseCollection;
    }

    public function destroy(DestroyRequest $request = null, Product $product = null, $fireWebhook = true)
    {
        if (!$product) {
            $input = $request->validated();
            $product = Product::where('id', $input['id'])->first();
        }

        $response = null;

        if (!empty($product) && $product->quantity_on_hand == 0) {
            $product->delete();

            $response = collect(['id' => $product->id, 'sku' => $product->sku, 'customer_id' => $product->customer_id]);

            if ($fireWebhook) {
                $this->webhook($response, Product::class, Webhook::OPERATION_TYPE_DESTROY, $product->customer_id);
            }
        }

        return $response;
    }

    public function destroyBatch(DestroyBatchRequest $request)
    {
        $responseCollection = new Collection();

        $input = $request->validated();

        foreach ($input as $record) {
            $destroyRequest = DestroyRequest::make($record);

            $response = $this->destroy($destroyRequest, null, false);

            if (!empty($response)) {
                $responseCollection->add($response);
            }
        }

        $this->batchWebhook($responseCollection, Product::class, ResourceCollection::class, Webhook::OPERATION_TYPE_DESTROY);

        return $responseCollection;
    }

    public function changeLocationQuantity(ChangeLocationQuantityRequest $request, Product $product): void
    {
        $input = $request->validated();

        $location = Location::find(Arr::get($input, 'location_id'));

        $quantity = Arr::get($input, 'quantity');
        if ($quantity === null) {
            $quantity = Arr::get($input, 'quantity_available');

            if ($quantity !== null) {
                if ($product->locations->count() == 1) {
                    $quantity += $product->orderItem()->sum('quantity_pending');
                }
            }
        }

        app('inventoryLog')->adjustInventory(
            $location,
            $product,
            $quantity,
            InventoryLogComponent::OPERATION_TYPE_MANUAL
        );
    }

    public function removeFromLocation(Product $product, $location_id): void
    {
        $lotItem = LotItem::where('location_id', $location_id)
            ->whereHas('lot', function($query) use ($product){
                $query->where('product_id', $product->id);
            })
            ->first();

        if (!empty($lotItem)) {
            $lotItem->delete();
        }

        $location = $product->locations()->where('location_id', $location_id)->first();

        if ($location && $location->pivot->quantity_on_hand == 0) {
            app('inventoryLog')->adjustInventory(
                $location,
                $product,
                0,
                InventoryLogComponent::OPERATION_TYPE_MANUAL
            );

            $product->locations()->detach($location);
            $product->save();
        }
    }

    public function removeEmptyLocations(Product $product): void
    {
        $locations = $product->locations()->wherePivot('quantity_on_hand', 0)->get();

        foreach ($locations as $location) {
            $product->locations()->detach($location);
        }

        $product->save();
    }

    public function addToLocation(Product $product, $location_id, $quantity): void
    {
        $location = Location::query()->find($location_id);

        if ($location) {
            app('inventoryLog')->adjustInventory(
                $location,
                $product,
                $quantity,
                InventoryLogComponent::OPERATION_TYPE_MANUAL
            );
        }
    }

    public function updateLocationProduct($product, $quantity, $fromLocation, $toLocation, $operation)
    {
        $location = Location::where('id', $fromLocation)->first();
        $associatedObject = Location::where('id', $toLocation)->first();

        app('inventoryLog')->adjustInventory($location, $product, $quantity, $operation, $associatedObject);
    }

    public function filterCustomers(Request $request): JsonResponse
    {
        $term = $request->get('term');
        $results = [];

        if ($term) {
            $contactInformation = Customer::whereHas('contactInformation', function($query) use ($term) {
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

    public function filterLocations(Request $request, Product $product): JsonResponse
    {
        $term = $request->get('term');
        $results = [];

        if ($term) {
            $term = $term . '%';

            $locations = Location::whereHas('products.product', static function ($query) use ($product) {
                $query->where('id', $product->id);
            })
                ->where('name', 'like', $term)
                ->where('id', '!=', $request->get('from_location_id'))
                ->get();

            foreach ($locations as $location) {
                $results[] = [
                    'id' => $location->id,
                    'text' => $location->name
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
    public function filterKitProducts(Request $request, Customer $customer = null): JsonResponse
    {
        $term = $request->get('term');
        $excludedIds = $request->get('excludedIds');
        $results = [];

        if ($term) {
            $term = $term . '%';

            $products = Product::query()
                ->where(function ($query) use ($term) {
                    return $query->where('sku', 'like', $term)
                        ->orWhere('name', 'like', $term);
                });

            if (!is_null($customer)) {
                $products = $products->where('customer_id', $customer->id);
            }

            if (!empty($excludedIds) && count($excludedIds)) {
                $products = $products->whereNotIn('id', $excludedIds);
            }

            $products = $products->get();

            foreach ($products as $product) {
                $results[] = [
                    'id' => $product->id,
                    'text' => $product->name
                ];
            }
        }

        return response()->json([
            'results' => $results
        ]);
    }

    public function getCustomerProduct(Customer $customer)
    {
        return $customer->products()->paginate();
    }

    /**
     * @param FilterRequest $request
     * @param $customerIds
     * @return LengthAwarePaginator
     */
    public function filter(FilterRequest $request, $customerIds)
    {
        $query = Product::query();

        $query->when($request['customer_id'], function ($q) use($request){
            return $q->where('customer_id', $request['customer_id']);
        });

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

        $query->when($request['location_id'], function ($q) use($request){
            return $q->whereHas('location', function ($loc) use($request){
                return $loc->where('locations.id', $request['location_id']);
            });
        });

        $query->when(count($customerIds) > 0, function ($q) use($customerIds){
            return $q->whereIn('customer_id', $customerIds);
        });

        return $query->paginate();
    }

    /**
     * @param Request $request
     * @param Supplier|null $supplier
     * @return array|Builder[]|Collection|\Illuminate\Database\Eloquent\Collection|Product[]
     */
    public function filterBySupplier(Request $request, Supplier $supplier = null)
    {
        $term = '%'.$request->get('term').'%';

        if (is_null($supplier)) {
            return [];
        }

        $prodQuery = Product::whereHas('suppliers', function ($query) use ($term, $supplier) {
            $query->where('suppliers.id', $supplier->id);
        })
        ->where(function ($query) use ($term) {
            $query->where('name', 'like', $term);
            $query->orWhere('sku', 'like', $term);
        });

        if (isset($request->lot)) {
            $prodQuery->where('lot_tracking', '1');
        }

        return $prodQuery->get();
    }
    /**
     * @param Request $request
     * @param Customer|null $customer
     * @return JsonResponse
     */
    public function filterSuppliers(Request $request, Customer $customer = null): JsonResponse
    {
        $term = $request->get('term');
        $excludedIds = $request->get('excludedIds') ?? [];

        $results = [];

        if ($term) {
            if (is_null($customer)) {
                if ($request->exists('product_id')) {
                    /** @var Product $product */
                    $product = Product::find($request->get('product_id'));

                    $suppliers = $product->customer->suppliers();

                    if ($product->customer->parent) {
                        $suppliers = $suppliers->orWhere('customer_id', $product->customer->parent->id);
                    }
                } else {
                    $customers = app('user')->getSelectedCustomers()->pluck('id')->toArray();

                    $suppliers = Supplier::whereIn('customer_id', $customers);
                }
            } else {
                $suppliers = $customer->suppliers();
            }

            $suppliers = $suppliers
                ->whereNotIn('id', $excludedIds)
                ->whereHas('contactInformation', function ($query) use ($term) {
                    $term = $term . '%';

                    $query->where('name', 'like', $term)
                        ->orWhere('company_name', 'like', $term)
                        ->orWhere('email', 'like', $term)
                        ->orWhere('zip', 'like', $term)
                        ->orWhere('city', 'like', $term)
                        ->orWhere('phone', 'like', $term);
                })->get();
        }

        if (isset($suppliers) && $suppliers->count()) {
            foreach ($suppliers as $supplier) {
                $results[] = [
                    'id' => $supplier->id,
                    'text' => $supplier->contactInformation->name . ', ' . $supplier->contactInformation->email . ', ' . $supplier->contactInformation->zip . ', ' . $supplier->contactInformation->city . ', ' . $supplier->contactInformation->phone
                ];
            }
        }

        return response()->json([
            'results' => $results
        ]);
    }

    public function recalculateQuantityOnHand(Product $product)
    {
        $quantityOnHand = LocationProduct::where('product_id', $product->id)->sum('quantity_on_hand');

        $product->update([
            'quantity_on_hand' => $quantityOnHand
        ]);

        return $quantityOnHand;
    }

    public function recalculateQuantityAvailable(Product $product): int
    {
        $product = $product->refresh();

        $quantityAvailable = $product->quantity_on_hand - $product->quantity_allocated + $this->getSellAheadQuantities($product) - $product->quantity_backordered;

        $product->update([
            'quantity_available' => max(0, $quantityAvailable)
        ]);

        return $quantityAvailable;
    }

    public function recalculateQuantityPickable(Product $product)
    {
        $quantityPickable = Product::query()
            ->where('products.id', $product->id)
            ->join('location_product', 'location_product.product_id','=', 'products.id')
            ->join('locations', 'location_product.location_id','=', 'locations.id')
            ->where('locations.pickable_effective', 1)
            ->sum('location_product.quantity_on_hand');

        $product->update([
            'quantity_pickable' => (int) $quantityPickable
        ]);

        return $quantityPickable;
    }

    private function getSellAheadQuantities(Product $product): int
    {
        $quantitySellAhead = 0;

        foreach ($product->purchaseOrderLine as $purchaseOrderItem) {
            $quantitySellAhead += $purchaseOrderItem->quantity_sell_ahead;
        }

        return $quantitySellAhead;
    }

    public function allocateInventory(Product $product): void
    {
        if (!$product->isKit()) {
            $quantityOnHand = $this->recalculateQuantityOnHand($product);
            $quantityOnHand = max(0, $quantityOnHand);

            $quantityPickable = $this->recalculateQuantityPickable($product);

            DB::transaction(static function() use ($product, $quantityPickable, $quantityOnHand) {
                DB::update("
                    UPDATE (
                        SELECT
                            `order_items_to_allocate`.*,
                            GREATEST({$quantityOnHand} + IF(`orders`.`allocation_hold` = 1, 0, `order_items_to_allocate`.`quantity_pending`) - SUM(`order_items_to_allocate`.`quantity_pending`) OVER (ORDER BY `orders`.`priority_score` DESC), 0) AS `remaining_on_hand`,
                            GREATEST({$quantityPickable} + IF(`orders`.`allocation_hold` = 1, 0, `order_items_to_allocate`.`quantity_pending`) - SUM(`order_items_to_allocate`.`quantity_pending`) OVER (ORDER BY `orders`.`priority_score` DESC), 0) AS `remaining_pickable`,
                            `orders`.`allocation_hold` as `order_allocation_hold`
                        FROM `order_items` `order_items_to_allocate`
                        LEFT JOIN `orders` ON `orders`.`id` = `order_items_to_allocate`.`order_id`
                        WHERE `product_id` = {$product->id}
                          AND (`quantity_pending` != 0 OR `quantity_allocated` != 0 OR `quantity_allocated_pickable` != 0 OR `quantity_backordered` != 0)
                        ORDER BY `orders`.`priority_score` DESC
                    ) `order_items_to_allocate`
                    LEFT JOIN `order_items` ON `order_items`.`id` = `order_items_to_allocate`.`id`
                    SET
                        `order_items`.`quantity_allocated` = LEAST(IF(`order_items_to_allocate`.`order_allocation_hold` = 1, 0, `order_items`.`quantity_pending`), `remaining_on_hand`),
                        `order_items`.`quantity_allocated_pickable` = LEAST(IF(`order_items_to_allocate`.`order_allocation_hold` = 1, 0, `order_items`.`quantity_pending`), `remaining_pickable`),
                        `order_items`.`quantity_backordered` = GREATEST(IF(`order_items_to_allocate`.`order_allocation_hold` = 1, 0, `order_items`.`quantity_pending`) - `remaining_on_hand`, 0)
                ");
            }, 10);

            $orderIdsToReprocess = $product->orderItem()
                ->where(function(Builder $query) {
                    $query->where('quantity_pending', '!=', 0)
                        ->orWhere('quantity_allocated', '!=', 0)
                        ->orWhere('quantity_allocated_pickable', '!=', 0)
                        ->orWhere('quantity_backordered', '!=', 0);
                })
                ->pluck('order_id')
                ->toArray();

            if (!empty($orderIdsToReprocess)) {
                app('order')->updateSummedQuantities($orderIdsToReprocess);
            }

            $this->updateSummedQuantities($product);
            $this->recalculateQuantityAvailable($product);

            foreach ($product->kitParents as $kitParent) {
                $this->calculateKitQuantities($kitParent);
            }
        } else {
            $this->calculateKitQuantities($product);
        }
    }

    public function allocateInventoryV2(Product $product): void
    {
        if (!$product->isKit()) {
            $quantityOnHand = $this->recalculateQuantityOnHand($product);
            $quantityOnHand = max(0, $quantityOnHand);
            $quantityOnHandRemaining = $quantityOnHand;

            $quantityPickable = $this->recalculateQuantityPickable($product);
            $quantityPickableRemaining = $quantityPickable;

            $quantityBackordered = 0;

            $orderItems = $product->orderItem()
                ->select('order_items.*', 'orders.allocation_hold')
                ->join('orders', 'order_items.order_id', '=', 'orders.id')
                ->where(function(Builder $query) {
                    $query->where('quantity_pending', '!=', 0)
                        ->orWhere('quantity_allocated', '!=', 0)
                        ->orWhere('quantity_allocated_pickable', '!=', 0)
                        ->orWhere('quantity_backordered', '!=', 0);
                })
                ->orderByDesc('orders.priority_score')
                ->get();

            $orderIdsToReprocess = [];

            foreach ($orderItems as $orderItem) {
                $orderItemQuantityPending = max($orderItem->quantity_pending, 0);

                if ($orderItem->allocation_hold) {
                    $orderItemQuantityAllocated = 0;
                    $orderItemQuantityAllocatedPickable = 0;
                    $orderItemQuantityBackordered = $orderItemQuantityPending;
                } else {
                    $quantityToAllocate = min($orderItemQuantityPending, $quantityOnHandRemaining);
                    $quantityToAllocatePickable = min($orderItemQuantityPending, $quantityPickableRemaining);

                    $orderItemQuantityAllocated = $quantityToAllocate;
                    $orderItemQuantityAllocatedPickable = $quantityToAllocatePickable;
                    $orderItemQuantityBackordered = $orderItemQuantityPending - $orderItemQuantityAllocated;

                    $quantityOnHandRemaining -= $quantityToAllocate;
                    $quantityPickableRemaining -= $quantityToAllocatePickable;
                }

                $orderItem->updateQuietly([
                    'quantity_allocated' => $orderItemQuantityAllocated,
                    'quantity_allocated_pickable' => $orderItemQuantityAllocatedPickable,
                    'quantity_backordered' => $orderItemQuantityBackordered
                ]);

                $quantityBackordered += $orderItemQuantityBackordered;

                $orderIdsToReprocess[$orderItem->order_id] = $orderItem->order_id;
            }

            if (!empty($orderIdsToReprocess)) {
                app('order')->updateSummedQuantitiesV2($orderIdsToReprocess);
            }

            $product->updateQuietly([
                'quantity_allocated' => $quantityOnHand - $quantityOnHandRemaining,
                'quantity_allocated_pickable' => $quantityPickable - $quantityPickableRemaining,
                'quantity_backordered' => $quantityBackordered,
                'quantity_to_replenish' => ($quantityOnHand - $quantityOnHandRemaining) - ($quantityPickable - $quantityPickableRemaining)
            ]);

            $this->recalculateQuantityAvailable($product);

            foreach ($product->kitParents as $kitParent) {
                $this->calculateKitQuantities($kitParent);
            }
        } else {
            $this->calculateKitQuantities($product);
        }
    }

    public function updateProductImageURLs(Product $product, $items)
    {
        foreach ($items as $item) {
            $image = Image::where('source', $item['source'])->where('object_type', Product::class)->where('object_id', $product->id)->first();

            if ($image) {
                continue;
            }

            $imageObj = new Image();
            $imageObj->source = $item['source'];
            $imageObj->filename = '';
            $imageObj->object()->associate($product);
            $imageObj->save();
        }
    }

    public function updateProductImages(Product $product, $images, Customer $customer)
    {
        foreach ($images as $image) {
            $filename = $image->store('public');
            $source = url(Storage::url($filename));

            $imageObj = new Image();
            $imageObj->source = $source;
            $imageObj->filename = $filename;
            $imageObj->object()->associate($product);
            $imageObj->save();

            $revision = new Revision();
            $revision->revisionable_type = get_class($product);
            $revision->revisionable_id = $product->id;
            $revision->user_id = $customer->id;
            $revision->key = 'Product Images';
            $revision->new_value = $filename;
            $revision->is_image = true;
            $revision->action = 'Added';
            $revision->save();
        }
    }

    public function deleteProductImage(Request $request)
    {
        $id = (int)$request->get('id');
        $success = false;

        if (($image = Image::get()->find($id)) && Storage::delete($image['filename'])) {
            $revisionableClass = get_class(new Product);
            if ($image->object_type === $revisionableClass) {
                $revision = new Revision();
                $revision->revisionable_type = $revisionableClass;
                $revision->revisionable_id = $image->object_id;
                $revision->user_id = app('user')->getSessionCustomer()->id ?? app('user')->getSelectedCustomers()->first()->id;
                $revision->key = 'Product Images';
                $revision->new_value = $image['filename'];
                $revision->is_image = true;
                $revision->action = 'Deleted';
                $revision->save();
            }

            $success = $image->delete();
        }

        return response()->json([
            'success' => $success
        ]);
    }

    public function getCountries()
    {
        return Countries::all();
    }

    public function recover(Product $product): void
    {
        $product->deleted_at = null;
        $product->save();
    }

    public function importCsv(ImportCsvRequest $request): string
    {
        $input = $request->validated();

        $importLines = app('csv')->getCsvData($input['import_csv']);

        $columns = array_intersect(
            app('csv')->unsetCsvHeader($importLines, 'sku'),
            ProductExportResource::columns()
        );

        if (!empty($importLines)) {
            $storedCollection = new Collection();
            $updatedCollection = new Collection();

            foreach ($importLines as $importLineIndex => $importLine) {
                $data = [];
                $data['customer_id'] = $input['customer_id'];

                foreach ($columns as $columnIndex => $column) {
                    if (Arr::has($importLine, $columnIndex)) {
                        $data[$column] = Arr::get($importLine, $columnIndex);
                    }
                }

                $product = Product::where('customer_id', $data['customer_id'])->where('sku', $data['sku'])->first();

                if ($product) {
                    $updatedCollection->add($this->update($this->createRequestFromImport($data, true), $product, false));
                } else {
                    $storedCollection->add($this->store($this->createRequestFromImport($data), false));
                }

                Session::flash('status', ['type' => 'info', 'message' => __('Importing :current/:total products', ['current' => $importLineIndex + 1, 'total' => count($importLines)])]);
                Session::save();
            }

            $this->batchWebhook($storedCollection, Product::class, ProductCollection::class, Webhook::OPERATION_TYPE_STORE);
            $this->batchWebhook($updatedCollection, Product::class, ProductCollection::class, Webhook::OPERATION_TYPE_UPDATE);
        }

        Session::flash('status', ['type' => 'success', 'message' => __('Products were successfully imported!')]);

        return __('Products were successfully imported!');
    }

    public function importKitItemsCsv(ImportCsvRequest $request, Product $product): string
    {
        $input = $request->validated();

        $customer = Customer::find($input['customer_id']);
        $csv = $input['import_csv'];
        $products = [];

        if (($open = fopen($csv, 'rb')) !== false) {
            while (($data = fgetcsv($open, 1000)) !== false) {
                $products[] = $data;
            }
            fclose($open);
        }

        if (in_array('quantity_on_hand', $products[0], true)) {
            $headers = $products[0];
            unset($products[0]);
        }

        $data = [
            'new' => 0,
            'skipped' => 0
        ];

        if (!empty($products) && $customer && isset($headers)) {
            foreach ($products as $productImport) {
                $productData = [];
                $productData['customer_id'] = $customer->id;

                foreach ($headers as $key => $header) {
                    $productData[$header] = $productImport[$key];
                }

                $image = $productData['images'];
                unset($productData['images']);

                $product = Product::create($productData);

                if ($product->save()) {
                    $data['new']++;

                    if (!is_null($image)) {
                        try {
                            $this->downloadImageFromCsv($image, $product);
                        } catch (\Exception $exception) {
                            Log::debug($exception);
                        }
                    }
                } else {
                    $data['skipped']++;
                }
            }
        }

        return $data['new'] . ' new products added and ' . $data['skipped'] . ' products were skipped';
    }

    private function downloadImageFromCsv(string $image, Product $product): void
    {
        if (filter_var($image, FILTER_VALIDATE_URL)) {
            $imageContent = file_get_contents($image);
            $filename = basename($image);

            Storage::disk('public')->put($filename, $imageContent);
            $source = url(Storage::url($filename));

            $imageObj = new Image();
            $imageObj->source = $source;
            $imageObj->filename = $filename;
            $imageObj->object()->associate($product);
            $imageObj->save();

            $revision = new Revision();
            $revision->revisionable_type = get_class($product);
            $revision->revisionable_id = $product->id;
            $revision->user_id = app('user')->getSessionCustomer()->id;
            $revision->key = 'Product Images';
            $revision->new_value = $filename;
            $revision->is_image = true;
            $revision->action = 'Added';
            $revision->save();
        }
    }

    /**
     * @param ExportCsvRequest $request
     * @return StreamedResponse
     */
    public function exportCsv(ExportCsvRequest $request): StreamedResponse
    {
        $input = $request->validated();
        $search = $input['search']['value'];

        $products = $this->getQuery($request->get('filter_form'));

        if ($search) {
            $products = $this->searchQuery($search, $products);
        }

        $csvFileName = Str::kebab(auth()->user()->contactInformation->name) . '-products-export.csv';

        return app('csv')->export($request, $products->get(), ProductExportResource::columns(), $csvFileName, ProductExportResource::class);
    }

    /**
     * @param array $data
     * @param bool $update
     * @return StoreRequest|UpdateRequest
     */
    private function createRequestFromImport(array $data, bool $update = false)
    {
        $requestData = $data;
        unset($requestData['image'], $requestData['locations'], $requestData['new_locations']);

        if (Arr::has($data, 'image')) {
            $requestData['product_images'] = [
                [
                    'source' => Arr::get($data, 'image')
                ]
            ];
        }

        if (isset($requestData['vendor']) && !empty($requestData['vendor'])) {
            unset($requestData['vendor']);

            $supplierNames = explode(';', $data['vendor']);
            $supplierIds = [];

            foreach ($supplierNames as $supplierName) {
                $supplier = Supplier::whereHas('contactInformation', static function ($query) use ($supplierName) {
                    $query->where('name', 'like', trim($supplierName));
                })
                    ->where('customer_id', $requestData['customer_id'])
                    ->first();

                if ($supplier) {
                    $supplierIds[] = $supplier->id;
                }
            }

            if (!empty($supplierIds)) {
                if ($update) {
                    $requestData['update_vendor'] = true;
                }

                $requestData['suppliers'] = $supplierIds;
            }
        }

        if ($update) {
            return UpdateRequest::make($requestData);
        }

        return StoreRequest::make($requestData);
    }

    /**
     * @param $filterInputs
     * @param string $sortColumnName
     * @param string $sortDirection
     * @return mixed
     */
    public function getQuery($filterInputs, string $sortColumnName = 'products.id', string $sortDirection = 'desc')
    {
        $customerIds = app('user')->getSelectedCustomers()->pluck('id')->toArray();

        $filterCustomerId = Arr::get($filterInputs, 'customer_id');

        if ($filterCustomerId && $filterCustomerId != 'all') {
            $customerIds = array_intersect($customerIds, [$filterCustomerId]);
        }

        $productCollection = Product::join('customers', 'products.customer_id', '=', 'customers.id')
            ->join('contact_informations AS customer_contact_information', 'customers.id', '=', 'customer_contact_information.object_id')
            ->leftJoin('product_supplier', 'products.id', '=', 'product_supplier.product_id')
            ->leftJoin('location_product', 'products.id', '=', 'location_product.product_id')
            ->where('customer_contact_information.object_type', Customer::class)
            ->whereIn('products.customer_id', $customerIds)
            ->where(function ($query) use ($filterInputs) {
                // Find by filter result

                // Allocated
                if (isset($filterInputs['allocated'])) {
                    $query->where('products.quantity_allocated', $filterInputs['allocated'] ? '>' : '=', 0);
                }

                // Backordered
                if (isset($filterInputs['backordered'])) {
                    $query->where('products.quantity_backordered', $filterInputs['backordered'] ? '>' : '=', 0);
                }

                // In Stock
                if (isset($filterInputs['in_stock'])) {
                    $query->where('products.quantity_available', $filterInputs['in_stock'] ? '>' : '=', 0);
                }

                // Is Kit
                if (isset($filterInputs['is_kit'])) {
                    if ($filterInputs['is_kit']) {
                        $query->whereHas('kitItems');
                    } else {
                        $query->whereDoesntHave('kitItems');
                    }
                }

                // Warehouse
                if (!is_null($filterInputs) && $filterInputs['warehouse'] && $filterInputs['warehouse'] !== 0) {
                    $locations = Location::query()->where('warehouse_id', $filterInputs['warehouse'])->pluck('id');

                    $query->whereIn('location_product.location_id', $locations ?? []);
                }

                // Vendor
                if (!is_null($filterInputs) && $filterInputs['supplier'] && $filterInputs['supplier'] !== 0) {
                    $query->where('product_supplier.supplier_id', $filterInputs['supplier']);
                }

                // Tags
                if (!is_null($filterInputs) && !empty($filterInputs['tags'])) {
                    $filterTags = (array) $filterInputs['tags'];
                    $query->whereHas('tags', function($query) use ($filterTags) {
                        $query->whereIn('name', $filterTags);
                    });
                }
            })
            ->select('products.*')
            ->groupBy('products.id')
            ->orderBy($sortColumnName, $sortDirection);

        // Show deleted products
        if (isset($filterInputs['show_deleted'])) {
            if ($filterInputs['show_deleted'] == 1) {
                $productCollection->onlyTrashed();
            } elseif ($filterInputs['show_deleted'] == 2) {
                $productCollection->withTrashed();
            }
        }

        return $productCollection;
    }

    /**
     * @param string $term
     * @param $productCollection
     * @return mixed
     */
    public function searchQuery(string $term, $productCollection): mixed
    {
        $term = $term . '%';

        return $productCollection
            ->where(function ($query) use ($term) {
                $query->where('products.name', 'like', $term)
                    ->orWhere('products.sku', 'like', $term)
                    ->orWhere('products.barcode', 'like', $term)
                    ->orWhere('products.price', 'like', $term);
                $query->orWhereHas('customer.contactInformation', function ($query) use ($term) {
                    $query->where('name', 'like', $term);
                });
            });
    }

    public function exportKitItemsCsv(Request $request, $product): StreamedResponse
    {
        $headers = array(
            "Content-type" => "text/csv",
            "Content-Disposition" => "attachment; filename=file.csv",
            "Pragma" => "no-cache",
            "Cache-Control" => "must-revalidate, post-check=0, pre-check=0",
            "Expires" => "0"
        );

        $customer = Customer::find($request->customer_id);

        $products = $product->kitItems->map(function ($item) {
            $item->quantity = $item->pivot->quantity;

            return $item;
        });

        $columns = ['quantity', 'sku', 'name', 'barcode', 'price', 'value', 'hs_code', 'weight', 'height', 'length', 'width', 'quantity_on_hand', 'quantity_available', 'quantity_allocated', 'quantity_backordered', 'notes', 'images', 'vendor'];

        $csvFileName = Str::kebab($customer->contactInformation->name) . '-export.csv';

        $callback = function () use ($products, $columns) {
            $file = fopen('php://output', 'wb');
            fputcsv($file, $columns);

            foreach ($products as $product) {
                $row = [];
                foreach ($columns as $column) {
                    $row[$column] = $product->$column;
                }

                if ($product->productImages) {
                    $images = [];
                    foreach ($product->productImages as $image) {
                        $images[] = $image->source;
                    }

                    $images = implode('|', $images);
                    $row['images'] = $images;
                }

                if ($product->suppliers) {
                    $suppliers = [];
                    foreach ($product->suppliers as $supplier) {
                        $suppliers[] = $supplier->contactInformation->name;
                    }

                    $suppliers = implode('|', $suppliers);
                    $row['vendor'] = $suppliers;
                }

                if (!empty($row)) {
                    fputcsv($file, $row);
                }
            }

            fclose($file);
        };

        return response()->streamDownload($callback, $csvFileName, $headers);
    }

    public function updateKitItems(Product $product, $request)
    {
        if ($request['kit-quantity']) {
            foreach ($request['kit-quantity'] as $key => $requestKitQuantity) {
                $kitItems = DB::table('kit_items')
                    ->where('parent_product_id', $product->id)
                    ->where('child_product_id', $key)
                    ->update(['quantity' => $requestKitQuantity]);
            }
        }

        $array = $product->kitItems->pluck('id')->toArray();
        foreach ($request->kit_items as $kit_item) {
            if (in_array($kit_item['id'], $array)) {
                $kitItems = DB::table('kit_items')
                    ->where('parent_product_id', $product->id)
                    ->where('child_product_id', $kit_item['id'])
                    ->update(['quantity' => $kit_item['quantity']]);
            } else{
                $kitItems = DB::table('kit_items')
                    ->insert([
                        'parent_product_id' => $product->id,
                        'child_product_id' => $kit_item['id'],
                        'quantity' => $kit_item['quantity']
                    ]);
            }
        }

        return $kitItems ?? '';
    }

    public function barcode(Product $product)
    {
        $generator = new BarcodeGeneratorPNG();

        $data = [
            'name' => $product->name,
            'barcode' => $generator->getBarcode($product->barcode, $generator::TYPE_CODE_128),
            'barcodeNumber' => $product->barcode
        ];

        $fileName = $product->name . Str::random(20) . '.pdf';

        $paperWidth = dimension_width($product->customer, 'barcode');
        $paperHeight = dimension_height($product->customer, 'barcode');

        return PDF::loadView('pdf.barcode', $data)
                    ->setPaper([0, 0, $paperWidth, $paperHeight])
                    ->stream($fileName);
    }

    public function barcodes(Request $request, Product $product)
    {
        $count = (int) $request->to_print;
        $printer_id = (int) $request->printer_id;

        if ($printer_id) {
            for ($i = 0; $i < $count; $i++) {
                PrintJob::create([
                    'object_type' => Product::class,
                    'object_id' => $product->id,
                    'url' => route('product.barcode', [
                        'product' => $product,
                    ]),
                    'printer_id' => $printer_id,
                    'user_id' => auth()->user()->id,
                ]);
            }
        }
    }

    public function getCustomerPrinters(Product $product): array
    {
        return $product->customer
            ->printers
            ->pluck('hostnameAndName', 'id')
            ->toArray();
    }

    /**
     * @param BulkEditRequest $request
     * @return void
     */
    public function bulkEdit(BulkEditRequest $request): void
    {
        $input = $request->validated();
        $productIds = explode(',', Arr::get($input, 'ids'));
        $updateColumns = [];

        if (!is_null($addTags = Arr::get($input, 'add_tags'))) {
            $this->bulkUpdateTags($addTags, $productIds, Product::class);
            Arr::forget($input, 'add_tags');
        }

        if (!is_null($removeTags = Arr::get($input, 'remove_tags'))) {
            $this->bulkRemoveTags($removeTags, $productIds);
            Arr::forget($input, 'remove_tags');
        }

        if (Arr::get($input, 'lot_tracking') !== '0') {
            $updateColumns['lot_tracking'] = 1;
        }

        if (Arr::get($input, 'has_serial_number') !== '0') {
            $updateColumns['has_serial_number'] = 1;
        }

        if (Arr::get($input, 'priority_counting_requested_at') !== '0') {
            $updateColumns['priority_counting_requested_at'] = 1;
        }

        if (!is_null(Arr::get($input, 'remove_empty_locations'))) {
            foreach ($productIds as $productId) {
                $this->removeEmptyLocations(Product::find($productId));
            }
        }

        if ($hsCode = Arr::get($input, 'hs_code')) {
            $updateColumns['hs_code'] = $hsCode;
        }

        if ($reorderThreshold = Arr::get($input, 'reorder_threshold')) {
            $updateColumns['reorder_threshold'] = $reorderThreshold;
        }

        if ($quantityReorder = Arr::get($input, 'quantity_reorder')) {
            $updateColumns['quantity_reorder'] = $quantityReorder;
        }

        if ($notes = Arr::get($input, 'notes')) {
            $updateColumns['notes'] = $notes;
        }

        if (!is_null($countryId = Arr::get($input, 'country_id'))) {
            $updateColumns['country_of_origin'] = $countryId;
        }

        if (!is_null($vendorId = Arr::get($input, 'vendor_id'))) {
            $updateColumns['update_vendor'] = true;
            $updateColumns['suppliers'][] = $vendorId;
        }

        $updateBatchRequest = [];

        foreach ($productIds as $productId) {
            $updateBatchRequest[] = ['id' => $productId] + $updateColumns;
        }

        $this->updateBatch(UpdateBatchRequest::make($updateBatchRequest));
    }

    /**
     * @param BulkSelectionRequest $request
     * @return void
     */
    public function bulkDelete(BulkSelectionRequest $request): void
    {
        $input = $request->validated();

        $productIds = explode(',', Arr::get($input, 'ids'));

        Product::whereIn('id', $productIds)->delete();
    }

    /**
     * @param BulkSelectionRequest $request
     * @return void
     */
    public function bulkRecover(BulkSelectionRequest $request): void
    {
        $input = $request->validated();

        $productIds = explode(',', Arr::get($input, 'ids'));

        Product::whereIn('id', $productIds)
            ->withTrashed()
            ->update([
                'deleted_at' => null
            ]);
    }


    /**
     * @param Request $request
     * @return JsonResponse
     */
    public function getBulkSelectionStatus(Request $request): JsonResponse
    {
        $deleted = true;
        $existing = true;

        $products = Product::whereIn('id', $request->get('ids'))
            ->withTrashed()
            ->get();

        foreach ($products as $product) {
            if ($product->trashed()) {
                $existing = false;
            } else {
                $deleted = false;
            }
        }

        return response()->json([
            'results' => compact('deleted', 'existing')
        ]);
    }


    private function updateSummedQuantities(Product $product)
    {
        DB::transaction(static function () use ($product) {
            DB::update(
                'UPDATE `products` LEFT JOIN
                        (SELECT
                            `product_id`,
                            SUM(`quantity_allocated`) AS `order_items_quantity_allocated_sum`,
                            SUM(`quantity_allocated_pickable`) AS `order_items_quantity_allocated_pickable_sum`,
                            SUM(`quantity_backordered`) AS `order_items_quantity_backordered_sum`
                        FROM `order_items`
                        WHERE
                            `product_id` = :product_id1
                            AND (`quantity_allocated` != 0 OR `quantity_allocated` != 0 OR `quantity_allocated_pickable` != 0 OR `quantity_backordered` != 0)
                        GROUP BY `product_id`) `summed_order_items`
                ON `products`.`id` = `summed_order_items`.`product_id`
                SET
                    `quantity_allocated` = IFNULL(`order_items_quantity_allocated_sum`, 0),
                    `quantity_allocated_pickable` = IFNULL(`order_items_quantity_allocated_pickable_sum`, 0),
                    `quantity_backordered` = IFNULL(`order_items_quantity_backordered_sum`, 0),
                    `quantity_to_replenish` = GREATEST(IFNULL(`order_items_quantity_allocated_sum`, 0) - IFNULL(`order_items_quantity_allocated_pickable_sum`, 0), 0)
                WHERE `products`.`id` = :product_id2',
                [
                    'product_id1' => $product->id,
                    'product_id2' => $product->id
                ]
            );
        }, 10);
    }

    /**
     * @param Request $request
     * @param Customer|null $customer
     * @return array|Collection|\Illuminate\Database\Eloquent\Collection|Product[]
     */
    public function filterProducts(Request $request, Customer $customer = null)
    {
        $term = $request->get('term');
        $products = [];

        if ($term) {
            $term = $term . '%';
            if (is_null($customer)) {
                $customer = app('user')->getSessionCustomer();
            }
            $products = Product::where('customer_id', $customer->id)
                ->where(static function ($query) use ($term) {
                    return $query->where('sku', 'like', $term)
                        ->orWhere('name', 'like', $term);
                })->get();
        }

        return $products;
    }

    public function queryChildProducts($productId)
    {
        return Product::query()
            ->with('productImages')
            ->select('*', 'products.*')
            ->join('kit_items', 'kit_items.child_product_id', '=', 'products.id')
            ->whereIn('products.id', DB::table('kit_items')
                ->where('parent_product_id', $productId)
                ->pluck('child_product_id')->toArray())
            ->where('kit_items.parent_product_id', $productId)
            ->groupBy('products.id')->get();
    }

    /**
     * @param Product $product
     * @return void
     */
    private function calculateKitQuantities(Product $product): void
    {
        $quantities = [
            'quantity_on_hand',
            'quantity_available',
            'quantity_allocated',
            'quantity_backordered'
        ];

        $kitItems = [];

        foreach ($product->kitItems as $kitItem) {
            foreach ($quantities as $quantity) {
                $kitItems[$quantity][] = intdiv($kitItem->$quantity, $kitItem->pivot->quantity);
            }
        }

        foreach ($quantities as $quantity) {
            if ($quantity == 'quantity_backordered') {
                $product->$quantity = empty($kitItems[$quantity]) ? 0 : max($kitItems[$quantity]);
            } else {
                $product->$quantity = empty($kitItems[$quantity]) ? 0 : min($kitItems[$quantity]);
            }
        }

        $product->quantity_pickable = 0;
        $product->quantity_allocated_pickable = 0;

        $product->saveQuietly();

        app('inventoryLog')->triggerAdjustInventoryWebhook($product);
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
                $product = Product::find($id);

                foreach ($tags as $tag) {
                    $product->tags()->where('name', 'LIKE', $tag)->delete();
                }
            }
        } catch (\Exception $exception) {
            Log::error($exception->getMessage());
        }
    }
}
