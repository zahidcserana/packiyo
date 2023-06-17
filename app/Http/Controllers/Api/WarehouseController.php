<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\ApiController;
use App\Http\Requests\Warehouses\DestroyBatchRequest;
use App\Http\Requests\Warehouses\StoreBatchRequest;
use App\Http\Requests\Warehouses\UpdateBatchRequest;
use App\Http\Resources\WarehouseCollection;
use App\Http\Resources\WarehouseResource;
use App\JsonApi\V1\Warehouses\WarehouseSchema;
use App\Models\Warehouse;
use App\Models\UserRole;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Resources\Json\ResourceCollection;
use LaravelJsonApi\Core\Responses\DataResponse;
use LaravelJsonApi\Laravel\Http\Controllers\Actions\FetchOne;
use LaravelJsonApi\Laravel\Http\Requests\AnonymousCollectionQuery;

/**
 * Class WarehouseController
 * @package App\Http\Controllers\Api
 * @group Warehouses
 */
class WarehouseController extends ApiController
{
    use FetchOne;

    public function __construct()
    {
        $this->authorizeResource(Warehouse::class);
    }

    /**
     * @param WarehouseSchema $schema
     * @param AnonymousCollectionQuery $request
     * @return DataResponse
     */
    public function index(WarehouseSchema $schema, AnonymousCollectionQuery $request): DataResponse
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
     * @param WarehouseSchema $schema
     * @param StoreBatchRequest $request
     * @param AnonymousCollectionQuery $collectionQuery
     * @return DataResponse
     */
    public function store(WarehouseSchema $schema, StoreBatchRequest $request, AnonymousCollectionQuery $collectionQuery): DataResponse
    {
        $storedIds = (app()->warehouse->storeBatch($request))->pluck('id');

        $models = $schema
            ->repository()
            ->queryAll()
            ->withRequest($collectionQuery)
            ->firstOrPaginate($collectionQuery->page());

        $models = $models->whereIn('id', $storedIds);

        return new DataResponse($models);
    }

    /**
     * @param WarehouseSchema $schema
     * @param UpdateBatchRequest $request
     * @param AnonymousCollectionQuery $collectionQuery
     * @return DataResponse
     */
    public function update(WarehouseSchema $schema, UpdateBatchRequest $request, AnonymousCollectionQuery $collectionQuery): DataResponse
    {
        $updatedIds = (app()->warehouse->updateBatch($request))->pluck('id');

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
                app()->warehouse->destroyBatch($request)
            )
        );
    }
}
