<?php

namespace App\Http\Controllers\Api;

use App\Http\Requests\PurchaseOrder\StoreBatchRequest;
use App\Http\Requests\PurchaseOrder\DestroyBatchRequest;
use App\Http\Requests\PurchaseOrder\UpdateBatchRequest;
use App\Http\Requests\PurchaseOrder\ReceiveBatchRequest;
use App\Http\Requests\PurchaseOrder\FilterRequest;
use App\Http\Requests\PurchaseOrderItem\RejectPurchaseOrderItemRequest;
use App\JsonApi\V1\PurchaseOrderItems\PurchaseOrderItemSchema;
use App\JsonApi\V1\PurchaseOrders\PurchaseOrderSchema;
use App\JsonApi\V1\Revisions\RevisionSchema;
use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Resources\Json\ResourceCollection;
use App\Models\PurchaseOrder;
use App\Models\UserRole;
use App\Models\PurchaseOrderItem;
use App\Http\Controllers\ApiController;
use LaravelJsonApi\Core\Responses\DataResponse;
use LaravelJsonApi\Laravel\Http\Controllers\Actions\FetchOne;
use LaravelJsonApi\Laravel\Http\Requests\AnonymousCollectionQuery;

/**
 * Class PurchaseOrderController
 * @package App\Http\Controllers\Api
 * @group Purchase Orders
 */
class PurchaseOrderController extends ApiController
{
    use FetchOne;

    public function __construct()
    {
        $this->authorizeResource(PurchaseOrder::class);
    }

    /**
     * @param PurchaseOrderSchema $schema
     * @param AnonymousCollectionQuery $request
     * @return DataResponse
     */
    public function index(PurchaseOrderSchema $schema, AnonymousCollectionQuery $request): DataResponse
    {
        $models = $schema
            ->repository()
            ->queryAll()
            ->withRequest($request)
            ->firstOrPaginate($request->page());

        $user = auth()->user();

        if ($user->user_role_id != UserRole::ROLE_ADMINISTRATOR) {
            $customerIds = $user->customers()->pluck('customer_user.customer_id')->unique()->toArray();
            $models = $models->whereIn('customer_id', $customerIds);
        }

        return new DataResponse($models);
    }

    /**
     * @param PurchaseOrderSchema $schema
     * @param StoreBatchRequest $request
     * @param AnonymousCollectionQuery $collectionQuery
     * @return DataResponse
     */
    public function store(PurchaseOrderSchema $schema, StoreBatchRequest $request, AnonymousCollectionQuery $collectionQuery): DataResponse
    {
        $storedIds = (app()->purchaseOrder->storeBatch($request))->pluck('id');

        $models = $schema
            ->repository()
            ->queryAll()
            ->withRequest($collectionQuery)
            ->firstOrPaginate($collectionQuery->page());

        $models = $models->whereIn('id', $storedIds);

        return new DataResponse($models);
    }

    /**
     * @param PurchaseOrderSchema $schema
     * @param UpdateBatchRequest $request
     * @param AnonymousCollectionQuery $collectionQuery
     * @return DataResponse
     */
    public function update(PurchaseOrderSchema $schema, UpdateBatchRequest $request, AnonymousCollectionQuery $collectionQuery): DataResponse
    {
        $updatedIds = (app()->purchaseOrder->updateBatch($request))->pluck('id');

        $models = $schema
            ->repository()
            ->queryAll()
            ->withRequest($collectionQuery)
            ->firstOrPaginate($collectionQuery->page());

        $models = $models->whereIn('id', $updatedIds);

        return new DataResponse($models);
    }
    /**
     * Remove the specified resource from storage.
     *
     * @param DestroyBatchRequest $request
     * @return JsonResponse
     */
    public function destroy(DestroyBatchRequest $request): JsonResponse
    {
        return response()->json(
            new ResourceCollection(
                app()->purchaseOrder->destroyBatch($request)
            )
        );
    }

    public function receive(PurchaseOrderItemSchema $schema, AnonymousCollectionQuery $collectionQuery, ReceiveBatchRequest $request, PurchaseOrder $purchaseOrder): DataResponse
    {
        $this->authorize('batchReceive', $purchaseOrder);

        $receivedIds =  (app()->purchaseOrder->receiveBatch($request, $purchaseOrder)->pluck('id'));
        $models = $schema
            ->repository()
            ->queryAll()
            ->withRequest($collectionQuery)
            ->firstOrPaginate($collectionQuery->page());

        $models = $models->whereIn('id', $receivedIds);

        return new DataResponse($models);
    }

    /**
     * @param RevisionSchema $schema
     * @param AnonymousCollectionQuery $collectionQuery
     * @param PurchaseOrder $purchaseOrder
     * @return DataResponse
     * @throws AuthorizationException
     */
    public function history(RevisionSchema $schema, AnonymousCollectionQuery $collectionQuery, PurchaseOrder $purchaseOrder): DataResponse
    {
        $this->authorize('history', $purchaseOrder);

        $revisionIds = (app()->purchaseOrder->history($purchaseOrder)->pluck('id'));

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
     * @param PurchaseOrderItem $purchaseOrderItem
     * @return DataResponse
     * @throws AuthorizationException
     */
    public function itemHistory(RevisionSchema $schema, AnonymousCollectionQuery $collectionQuery, PurchaseOrderItem $purchaseOrderItem): DataResponse
    {
        $this->authorize('itemHistory', $purchaseOrderItem->purchaseOrder);
        $revisionIds = (app()->purchaseOrder->history($purchaseOrderItem)->pluck('id'));

        $models = $schema
            ->repository()
            ->queryAll()
            ->withRequest($collectionQuery)
            ->firstOrPaginate($collectionQuery->page());

        $models = $models->whereIn('id', $revisionIds);

        return new DataResponse($models);
    }

    /**
     * @param PurchaseOrderSchema $schema
     * @param AnonymousCollectionQuery $collectionQuery
     * @param FilterRequest $request
     * @return DataResponse
     */
    public function filter(PurchaseOrderSchema $schema, AnonymousCollectionQuery $collectionQuery, FilterRequest $request): DataResponse
    {
        $user = auth()->user();

        $customerIds = [];

        if ($user->user_role_id != UserRole::ROLE_ADMINISTRATOR) {
            $customerIds = $user->customers()->pluck('customer_user.customer_id')->unique()->toArray();
        }

        $purchaseOrderIds = (app()->purchaseOrder->filter($request, $customerIds)->pluck('id'));

        $models = $schema
            ->repository()
            ->queryAll()
            ->withRequest($collectionQuery)
            ->firstOrPaginate($collectionQuery->page());

        $models = $models->whereIn('id', $purchaseOrderIds);

        return new DataResponse($models);
    }

    /**
     * @throws AuthorizationException
     */
    public function close(PurchaseOrderSchema $schema, AnonymousCollectionQuery $collectionQuery, PurchaseOrder $purchaseOrder): DataResponse
    {
        $this->authorize('close', $purchaseOrder);

        app()->purchaseOrder->closePurchaseOrder($purchaseOrder);

        $models = $schema
            ->repository()
            ->queryAll()
            ->withRequest($collectionQuery)
            ->firstOrPaginate($collectionQuery->page());

        $models = $models->whereIn('id', $purchaseOrder->id);

        return new DataResponse($models);
    }

    /**
     * @throws AuthorizationException
     */
    public function reject(PurchaseOrderSchema $schema, AnonymousCollectionQuery $collectionQuery, RejectPurchaseOrderItemRequest $request, PurchaseOrderItem $purchaseOrderItem): DataResponse
    {
        $this->authorize('reject', $purchaseOrderItem->purchaseOrder);

        app()->purchaseOrder->rejectPurchaseOrderItem($request, $purchaseOrderItem);

        $models = $schema
            ->repository()
            ->queryAll()
            ->withRequest($collectionQuery)
            ->firstOrPaginate($collectionQuery->page());

        $models = $models->whereIn('id', $purchaseOrderItem->purchaseOrder->id);

        return new DataResponse($models);
    }
}
