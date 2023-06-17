<?php

namespace App\Http\Controllers\Api;

use App\Http\Requests\ShippingBox\DestroyBatchRequest;
use App\Http\Requests\ShippingBox\StoreBatchRequest;
use App\Http\Requests\ShippingBox\UpdateBatchRequest;
use App\Http\Resources\ShippingBoxCollection;
use App\Http\Resources\ShippingBoxResource;
use App\JsonApi\V1\ShippingBoxes\ShippingBoxSchema;
use App\Models\ShippingBox;
use App\Models\UserRole;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Resources\Json\ResourceCollection;
use App\Http\Controllers\ApiController;
use LaravelJsonApi\Core\Responses\DataResponse;
use LaravelJsonApi\Laravel\Http\Controllers\Actions\FetchOne;
use LaravelJsonApi\Laravel\Http\Requests\AnonymousCollectionQuery;


/**
 * Class ShippingBoxController
 * @package App\Http\Controllers\Api
 * @group Order Statuses
 */
class ShippingBoxController extends ApiController
{
    use FetchOne;

    public function __construct()
    {
        $this->authorizeResource(ShippingBox::class);
    }

    /**
     * @param ShippingBoxSchema $schema
     * @param AnonymousCollectionQuery $request
     * @return DataResponse
     */
    public function index(ShippingBoxSchema $schema, AnonymousCollectionQuery $request): DataResponse
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
     * @param ShippingBoxSchema $schema
     * @param StoreBatchRequest $request
     * @param AnonymousCollectionQuery $collectionQuery
     * @return DataResponse
     */
    public function store(ShippingBoxSchema $schema, StoreBatchRequest $request, AnonymousCollectionQuery $collectionQuery): DataResponse
    {
        $storedIds = (app()->shippingBox->storeBatch($request))->pluck('id');

        $models = $schema
            ->repository()
            ->queryAll()
            ->withRequest($collectionQuery)
            ->firstOrPaginate($collectionQuery->page());

        $models = $models->whereIn('id', $storedIds);

        return new DataResponse($models);
    }

    /**
     * @param ShippingBoxSchema $schema
     * @param UpdateBatchRequest $request
     * @param AnonymousCollectionQuery $collectionQuery
     * @return DataResponse
     */
    public function update(ShippingBoxSchema $schema, UpdateBatchRequest $request, AnonymousCollectionQuery $collectionQuery): DataResponse
    {
        $updatedIds = (app()->shippingBox->updateBatch($request))->pluck('id');

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
                app()->shippingBox->destroyBatch($request)
            )
        );
    }
}
