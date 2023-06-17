<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\ApiController;
use App\Http\Requests\Location\DestroyBatchRequest;
use App\Http\Requests\Location\StoreBatchRequest;
use App\Http\Requests\Location\UpdateBatchRequest;
use App\Http\Resources\LocationCollection;
use App\Http\Resources\LocationResource;
use App\JsonApi\V1\Locations\LocationSchema;
use App\Models\Location;
use App\Models\UserRole;
use App\Models\Warehouse;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Resources\Json\ResourceCollection;
use LaravelJsonApi\Core\Responses\DataResponse;
use LaravelJsonApi\Laravel\Http\Controllers\Actions\FetchMany;
use LaravelJsonApi\Laravel\Http\Controllers\Actions\FetchOne;
use LaravelJsonApi\Laravel\Http\Requests\AnonymousCollectionQuery;

/**
 * Class LocationController
 * @package App\Http\Controllers\Api
 * @group Locations
 */
class LocationController extends ApiController
{
    use FetchOne;
    use FetchMany;

    public function __construct()
    {
        $this->authorizeResource(Location::class);
    }

    /**
     * @param LocationSchema $schema
     * @param AnonymousCollectionQuery $request
     * @return DataResponse
     */
    public function index(LocationSchema $schema, AnonymousCollectionQuery $request): DataResponse
    {
        $models = $schema
            ->repository()
            ->queryAll()
            ->withRequest($request)
            ->query();

        $customerIds = app('user')->getSelectedCustomers()->pluck('id')->toArray();

        $models = $models->whereHas('warehouse', function($warehouse) use ($customerIds) {
            $warehouse->whereIn('customer_id', $customerIds);
        });

        if ($request->page()) {
            $models = $models->paginate($request->page());
        } else {
            $models = $models->get();
        }

        return new DataResponse($models);
    }

    /**
     * @param LocationSchema $schema
     * @param StoreBatchRequest $request
     * @param AnonymousCollectionQuery $collectionQuery
     * @return DataResponse
     */
    public function store(LocationSchema $schema, StoreBatchRequest $request, AnonymousCollectionQuery $collectionQuery): DataResponse
    {
        $storedIds = (app()->location->storeBatch($request))->pluck('id');

        $models = $schema
            ->repository()
            ->queryAll()
            ->withRequest($collectionQuery)
            ->firstOrPaginate($collectionQuery->page());

        $models = $models->whereIn('id', $storedIds);

        return new DataResponse($models);
    }

    /**
     * @param LocationSchema $schema
     * @param UpdateBatchRequest $request
     * @param AnonymousCollectionQuery $collectionQuery
     * @return DataResponse
     */
    public function update(LocationSchema $schema, UpdateBatchRequest $request, AnonymousCollectionQuery $collectionQuery): DataResponse
    {
        $updatedIds = (app()->location->updateBatch($request))->pluck('id');

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
                app()->location->destroyBatch($request)
            )
        );
    }
}
