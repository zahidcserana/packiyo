<?php

namespace App\Http\Controllers\Api;

use App\Http\Requests\Order\StoreBatchRequest;
use App\Http\Requests\Order\DestroyBatchRequest;
use App\Http\Requests\Order\UpdateBatchRequest;
use App\Http\Requests\Order\FilterRequest;
use App\Http\Requests\Shipment\ShipRequest;
use App\Http\Requests\Tote\PickOrderItemsByBarcodeRequest;
use App\JsonApi\V1\Orders\OrderSchema;
use App\JsonApi\V1\Revisions\RevisionSchema;
use App\JsonApi\V1\Shipments\ShipmentSchema;
use App\JsonApi\V1\ToteOrderItems\PlacedToteOrderItemSchema;
use App\Models\Customer;
use App\Models\Location;
use App\Models\Tote;
use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Resources\Json\ResourceCollection;
use App\Models\Order;
use App\Models\UserRole;
use App\Models\OrderItem;
use App\Http\Controllers\ApiController;
use LaravelJsonApi\Core\Responses\DataResponse;
use LaravelJsonApi\Laravel\Http\Controllers\Actions\FetchOne;
use LaravelJsonApi\Laravel\Http\Requests\AnonymousCollectionQuery;

/**
 * Class OrderController
 * @package App\Http\Controllers\Api
 * @group Orders
 */
class OrderController extends ApiController
{
    use FetchOne;

    public function __construct()
    {
        $this->authorizeResource(Order::class);
    }

    /**
     * @param OrderSchema $schema
     * @param AnonymousCollectionQuery $request
     * @return DataResponse
     */
    public function index(OrderSchema $schema, AnonymousCollectionQuery $request): DataResponse
    {
        $customerIds = Auth()->user()->customerIds(true, true);

        if (in_array($request->customer_id, $customerIds)) {
            $customerIds = Customer::where('id', $request->customer_id)->orWhere('parent_id', $request->customer_id)->pluck('id')->toArray();
        } else {
            $customerIds = [];
        }

        $models = $schema
            ->repository()
            ->queryAll()
            ->withRequest($request)
            ->query();

        $models = $models->whereIn('customer_id', $customerIds);

        if ($request->page()) {
            $models = $models->paginate($request->page());
        } else {
            $models = $models->get();
        }

        return new DataResponse($models);
    }

    /**
     * @param OrderSchema $schema
     * @param StoreBatchRequest $request
     * @param AnonymousCollectionQuery $collectionQuery
     * @return DataResponse
     */
    public function store(OrderSchema $schema, StoreBatchRequest $request, AnonymousCollectionQuery $collectionQuery): DataResponse
    {
        $storedIds = (app()->order->storeBatch($request))->pluck('id');

        $models = $schema
            ->repository()
            ->queryAll()
            ->withRequest($collectionQuery)
            ->query();

        $models = $models->whereIn('id', $storedIds);

        if ($collectionQuery->page()) {
            $models = $models->paginate($collectionQuery->page());
        } else {
            $models = $models->get();
        }

        return new DataResponse($models);
    }

    /**
     * @param OrderSchema $schema
     * @param UpdateBatchRequest $request
     * @param AnonymousCollectionQuery $collectionQuery
     * @return DataResponse
     */
    public function update(OrderSchema $schema, UpdateBatchRequest $request, AnonymousCollectionQuery $collectionQuery): DataResponse
    {
        $updatedIds = (app()->order->updateBatch($request))->pluck('id');

        $models = $schema
            ->repository()
            ->queryAll()
            ->withRequest($collectionQuery)
            ->query();

        $models = $models->whereIn('id', $updatedIds);

        if ($collectionQuery->page()) {
            $models = $models->paginate($collectionQuery->page());
        } else {
            $models = $models->get();
        }

        return new DataResponse($models);
    }

    /**
     * @param DestroyBatchRequest $request
     * @return JsonResponse
     */
    public function destroy(DestroyBatchRequest $request): JsonResponse
    {
        return response()->json(
            new ResourceCollection(
                app()->order->destroyBatch($request)
            )
        );
    }

    /**
     * @param ShipmentSchema $schema
     * @param AnonymousCollectionQuery $collectionQuery
     * @param ShipRequest $request
     * @param Order $order
     * @return DataResponse
     * @throws AuthorizationException
     */
    public function ship(ShipmentSchema $schema, AnonymousCollectionQuery $collectionQuery, ShipRequest $request, Order $order): DataResponse
    {
        $this->authorize('ship', $order);

        $shipIds =  app()->shipment->ship($request, $order);

        $models = $schema
            ->repository()
            ->queryAll()
            ->withRequest($collectionQuery)
            ->firstOrPaginate($collectionQuery->page());

        $models = $models->whereIn('id', $shipIds);

        return new DataResponse($models);
    }

    /**
     * @param OrderSchema $schema
     * @param AnonymousCollectionQuery $collectionQuery
     * @param Order $order
     * @return DataResponse
     * @throws AuthorizationException
     */
    public function cancel(OrderSchema $schema, AnonymousCollectionQuery $collectionQuery, Order $order): DataResponse
    {
        $this->authorize('cancel', $order);

        if (!$order->fulfilled_at && !$order->cancelled_at) {
            dispatch(function() use ($order) {
                app('order')->cancelOrder($order);
            });
        }

        $models = $schema
            ->repository()
            ->queryAll()
            ->withRequest($collectionQuery)
            ->query();

        $models = $models->where('id', $order->id);

        if ($collectionQuery->page()) {
            $models = $models->paginate($collectionQuery->page());
        } else {
            $models = $models->get();
        }

        return new DataResponse($models);
    }

    /**
     * @param OrderSchema $schema
     * @param AnonymousCollectionQuery $collectionQuery
     * @param Order $order
     * @return DataResponse
     * @throws AuthorizationException
     */
    public function markAsFulfilled(OrderSchema $schema, AnonymousCollectionQuery $collectionQuery, Order $order): DataResponse
    {
        $this->authorize('markAsFulfilled', $order);

        dispatch(function() use ($order) {
            app('order')->markAsFulfilled($order);
        });

        $models = $schema
            ->repository()
            ->queryAll()
            ->withRequest($collectionQuery)
            ->query();

        $models = $models->where('id', $order->id);

        if ($collectionQuery->page()) {
            $models = $models->paginate($collectionQuery->page());
        } else {
            $models = $models->get();
        }

        return new DataResponse($models);
    }

    /**
     * @param RevisionSchema $schema
     * @param AnonymousCollectionQuery $collectionQuery
     * @param Order $order
     * @return DataResponse
     * @throws AuthorizationException
     */
    public function history(RevisionSchema $schema, AnonymousCollectionQuery $collectionQuery, Order $order): DataResponse
    {
        $this->authorize('history', $order);

        $revisionIds = (app()->order->history($order)->pluck('id'));

        $models = $schema
            ->repository()
            ->queryAll()
            ->withRequest($collectionQuery)
            ->firstOrPaginate($collectionQuery->page());

        $models = $models->whereIn('id', $revisionIds);

        return new DataResponse($models);
    }

    /**
     * @param RevisionSchema $schema
     * @param AnonymousCollectionQuery $collectionQuery
     * @param OrderItem $orderItem
     * @return DataResponse
     * @throws AuthorizationException
     */
    public function itemHistory(RevisionSchema $schema, AnonymousCollectionQuery $collectionQuery, OrderItem $orderItem): DataResponse
    {
        $this->authorize('itemHistory', $orderItem->order);

        $revisionIds = (app()->order->history($orderItem)->pluck('id'));

        $models = $schema
            ->repository()
            ->queryAll()
            ->withRequest($collectionQuery)
            ->firstOrPaginate($collectionQuery->page());

        $models = $models->whereIn('id', $revisionIds);

        return new DataResponse($models);
    }

    /**
     * @param OrderSchema $schema
     * @param AnonymousCollectionQuery $collectionQuery
     * @param FilterRequest $request
     * @return DataResponse
     */
    public function filter(OrderSchema $schema, AnonymousCollectionQuery $collectionQuery, FilterRequest $request): DataResponse
    {
        $user = auth()->user();

        $customerIds = [];

        if ($user->user_role_id != UserRole::ROLE_ADMINISTRATOR) {
            $customerIds = $user->customers()->pluck('customer_user.customer_id')->unique()->toArray();
        }

        $orderIds = (app()->order->filter($request, $customerIds)->pluck('id'));

        $models = $schema
            ->repository()
            ->queryAll()
            ->withRequest($collectionQuery)
            ->firstOrPaginate($collectionQuery->page());

        $models = $models->whereIntegerInRaw('id', $orderIds);

        return new DataResponse($models);
    }

    /**
     * @param PlacedToteOrderItemSchema $schema
     * @param Order $order
     * @param PickOrderItemsByBarcodeRequest $request
     * @param AnonymousCollectionQuery $collectionQuery
     * @return DataResponse
     * @throws AuthorizationException
     */
    public function pickOrderItems(PlacedToteOrderItemSchema $schema, Order $order, PickOrderItemsByBarcodeRequest $request, AnonymousCollectionQuery $collectionQuery): DataResponse
    {
        $tote = Tote::where('barcode', $request['tote_barcode'])->first();
        $location = Location::where('barcode', $request['location_barcode'])->first();

        $this->authorize('view', $tote);

        $toteItemIds = (app()->tote->pickOrderItemsByBarcode($request, $tote, $order, $location)) ?? [];

        if (!empty($toteItemIds)) {
            $toteItemIds = $toteItemIds->pluck('id');
        }

        $models = $schema
            ->repository()
            ->queryAll()
            ->withRequest($collectionQuery)
            ->firstOrPaginate($collectionQuery->page());

        $models = $models->whereIntegerInRaw('id', $toteItemIds);

        return new DataResponse($models);
    }
}
