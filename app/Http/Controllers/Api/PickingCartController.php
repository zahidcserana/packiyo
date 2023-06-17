<?php

namespace App\Http\Controllers\Api;

use App\Http\Requests\PickingCart\DestroyBatchRequest;
use App\Http\Requests\PickingCart\PickRequest;
use App\Http\Requests\PickingCart\StoreBatchRequest;
use App\Http\Requests\PickingCart\UpdateBatchRequest;
use App\Http\Resources\PickingCartResource;
use App\JsonApi\V1\PickingCarts\PickingCartsSchema;
use App\Models\PickingBatch;
use App\Models\PickingCart;
use App\Models\Product;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Resources\Json\ResourceCollection;
use App\Http\Controllers\ApiController;
use LaravelJsonApi\Core\Responses\DataResponse;
use LaravelJsonApi\Laravel\Http\Controllers\Actions\FetchOne;
use LaravelJsonApi\Laravel\Http\Requests\AnonymousCollectionQuery;

/**
 * Class PickingCartController
 * @package App\Http\Controllers\Api
 * @group PickingCarts
 */
class PickingCartController extends ApiController
{
    use FetchOne;

    public function __construct()
    {
        $this->authorizeResource(PickingCart::class);
    }

    /**
     * @param PickingCartsSchema $schema
     * @param AnonymousCollectionQuery $request
     * @return DataResponse
     */
    public function index(PickingCartsSchema $schema, AnonymousCollectionQuery $request): DataResponse
    {
        $models = $schema
            ->repository()
            ->queryAll()
            ->withRequest($request)
            ->firstOrPaginate($request->page());

        return new DataResponse($models);
    }

    /**
     * @param PickingCartsSchema $schema
     * @param StoreBatchRequest $request
     * @param AnonymousCollectionQuery $collectionQuery
     * @return DataResponse
     */
    public function store(PickingCartsSchema $schema, StoreBatchRequest $request, AnonymousCollectionQuery $collectionQuery): DataResponse
    {
        $storedIds = (app()->pickingCart->storeBatch($request))->pluck('id');

        $models = $schema
            ->repository()
            ->queryAll()
            ->withRequest($collectionQuery)
            ->firstOrPaginate($collectionQuery->page());

        $models = $models->whereIn('id', $storedIds);

        return new DataResponse($models);
    }

    /**
     * @param PickingCartsSchema $schema
     * @param UpdateBatchRequest $request
     * @param AnonymousCollectionQuery $collectionQuery
     * @return DataResponse
     */
    public function update(PickingCartsSchema $schema, UpdateBatchRequest $request, AnonymousCollectionQuery $collectionQuery): DataResponse
    {
        $updatedIds = (app()->pickingCart->updateBatch($request))->pluck('id');

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
                app()->pickingCart->destroyBatch($request)
            )
        );
    }

    public function addItemToCart(PickRequest $request, PickingBatch $pickingBatch): JsonResponse
    {
        $input = $request->validated();
        $product = Product::whereId($input['product_id'])->first();

        return response()->json(
            new PickingCartResource(
                app()->pickingCart->addItemToPickingCart($pickingBatch, $product)
            )
        );
    }
}
