<?php

namespace App\Http\Controllers\Api;

use App\Http\Requests\OrderStatus\DestroyBatchRequest;
use App\Http\Requests\OrderStatus\StoreBatchRequest;
use App\Http\Requests\OrderStatus\UpdateBatchRequest;
use App\JsonApi\V1\OrderStatuses\OrderStatusSchema;
use App\Models\Customer;
use App\Models\OrderStatus;
use App\Models\UserRole;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Resources\Json\ResourceCollection;
use App\Http\Controllers\ApiController;
use LaravelJsonApi\Core\Responses\DataResponse;
use LaravelJsonApi\Laravel\Http\Controllers\Actions\FetchOne;
use LaravelJsonApi\Laravel\Http\Requests\AnonymousCollectionQuery;

/**
 * Class OrderStatusController
 * @package App\Http\Controllers\Api
 * @group Order Statuses
 */
class OrderStatusController extends ApiController
{
    use FetchOne;

    public function __construct()
    {
        $this->authorizeResource(OrderStatus::class);
    }

    /**
     * @param OrderStatusSchema $schema
     * @param AnonymousCollectionQuery $request
     * @return DataResponse
     */
    public function index(OrderStatusSchema $schema, AnonymousCollectionQuery $request): DataResponse
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
            ->firstOrPaginate($request->page());

        $models = $models->whereIn('customer_id', $customerIds);

        return new DataResponse($models);
    }

    /**
     * @param OrderStatusSchema $schema
     * @param StoreBatchRequest $request
     * @param AnonymousCollectionQuery $collectionQuery
     * @return DataResponse
     */
    public function store(OrderStatusSchema $schema, StoreBatchRequest $request, AnonymousCollectionQuery $collectionQuery): DataResponse
    {
        $storedIds = (app()->orderStatus->storeBatch($request))->pluck('id');

        $models = $schema
            ->repository()
            ->queryAll()
            ->withRequest($collectionQuery)
            ->firstOrPaginate($collectionQuery->page());

        $models = $models->whereIn('id', $storedIds);

        return new DataResponse($models);
    }

    /**
     * @param OrderStatusSchema $schema
     * @param UpdateBatchRequest $request
     * @param AnonymousCollectionQuery $collectionQuery
     * @return DataResponse
     */
    public function update(OrderStatusSchema $schema, UpdateBatchRequest $request, AnonymousCollectionQuery $collectionQuery): DataResponse
    {
        $updatedIds = (app()->orderStatus->updateBatch($request))->pluck('id');

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
                app()->orderStatus->destroyBatch($request)
            )
        );
    }
}
