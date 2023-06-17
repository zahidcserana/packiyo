<?php

namespace App\Http\Controllers\Api;

use App\Http\Requests\PurchaseOrderStatus\DestroyBatchRequest;
use App\Http\Requests\PurchaseOrderStatus\StoreBatchRequest;
use App\Http\Requests\PurchaseOrderStatus\UpdateBatchRequest;
use App\JsonApi\V1\PurchaseOrderStatuses\PurchaseOrderStatusSchema;
use App\Models\PurchaseOrderStatus;
use App\Models\UserRole;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Resources\Json\ResourceCollection;
use App\Http\Controllers\ApiController;
use LaravelJsonApi\Core\Responses\DataResponse;
use LaravelJsonApi\Laravel\Http\Controllers\Actions\FetchOne;
use LaravelJsonApi\Laravel\Http\Requests\AnonymousCollectionQuery;

/**
 * Class PurchaseOrderStatusController
 * @package App\Http\Controllers\Api
 * @group Purchase Order Statuses
 */
class PurchaseOrderStatusController extends ApiController
{
    use FetchOne;

    public function __construct()
    {
        $this->authorizeResource(PurchaseOrderStatus::class);
    }

    /**
     * @param PurchaseOrderStatusSchema $schema
     * @param AnonymousCollectionQuery $request
     * @return DataResponse
     */
    public function index(PurchaseOrderStatusSchema $schema, AnonymousCollectionQuery $request): DataResponse
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
     * @param PurchaseOrderStatusSchema $schema
     * @param StoreBatchRequest $request
     * @param AnonymousCollectionQuery $collectionQuery
     * @return DataResponse
     */
    public function store(PurchaseOrderStatusSchema $schema, StoreBatchRequest $request, AnonymousCollectionQuery $collectionQuery): DataResponse
    {
        $storedIds = (app()->purchaseOrderStatus->storeBatch($request))->pluck('id');

        $models = $schema
            ->repository()
            ->queryAll()
            ->withRequest($collectionQuery)
            ->firstOrPaginate($collectionQuery->page());

        $models = $models->whereIn('id', $storedIds);

        return new DataResponse($models);
    }

    /**
     * @param PurchaseOrderStatusSchema $schema
     * @param UpdateBatchRequest $request
     * @param AnonymousCollectionQuery $collectionQuery
     * @return DataResponse
     */
    public function update(PurchaseOrderStatusSchema $schema, UpdateBatchRequest $request, AnonymousCollectionQuery $collectionQuery): DataResponse
    {
        $updatedIds = (app()->purchaseOrderStatus->updateBatch($request))->pluck('id');

        $models = $schema
            ->repository()
            ->queryAll()
            ->withRequest($collectionQuery)
            ->firstOrPaginate($collectionQuery->page());

        $models = $models->whereIn('id', $updatedIds);

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
                app()->purchaseOrderStatus->destroyBatch($request)
            )
        );
    }
}
