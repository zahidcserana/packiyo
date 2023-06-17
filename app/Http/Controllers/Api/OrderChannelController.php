<?php

namespace App\Http\Controllers\Api;

use App\Http\Requests\OrderChannel\DestroyBatchRequest;
use App\Http\Requests\OrderChannel\StoreBatchRequest;
use App\Http\Requests\OrderChannel\UpdateBatchRequest;
use App\JsonApi\V1\OrderChannels\OrderChannelSchema;
use App\Models\OrderChannel;
use App\Models\UserRole;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Resources\Json\ResourceCollection;
use App\Http\Controllers\ApiController;
use LaravelJsonApi\Core\Responses\DataResponse;
use LaravelJsonApi\Laravel\Http\Controllers\Actions\FetchOne;
use LaravelJsonApi\Laravel\Http\Requests\AnonymousCollectionQuery;

/**
 * Class orderChannelController
 * @package App\Http\Controllers\Api
 * @group Order Channels
 */
class OrderChannelController extends ApiController
{
    use FetchOne;

    public function __construct()
    {
        $this->authorizeResource(OrderChannel::class);
    }

    /**
     * @param OrderChannelSchema $schema
     * @param AnonymousCollectionQuery $request
     * @return DataResponse
     */
    public function index(OrderChannelSchema $schema, AnonymousCollectionQuery $request): DataResponse
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
     * @param OrderChannelSchema $schema
     * @param StoreBatchRequest $request
     * @param AnonymousCollectionQuery $collectionQuery
     * @return DataResponse
     */
    public function store(OrderChannelSchema $schema, StoreBatchRequest $request, AnonymousCollectionQuery $collectionQuery): DataResponse
    {
        $storedIds = (app('orderChannel')->storeBatch($request))->pluck('id');

        $models = $schema
            ->repository()
            ->queryAll()
            ->withRequest($collectionQuery)
            ->firstOrPaginate($collectionQuery->page());

        $models = $models->whereIn('id', $storedIds);

        return new DataResponse($models);
    }

    /**
     * @param OrderChannelSchema $schema
     * @param UpdateBatchRequest $request
     * @param AnonymousCollectionQuery $collectionQuery
     * @return DataResponse
     */
    public function update(OrderChannelSchema $schema, UpdateBatchRequest $request, AnonymousCollectionQuery $collectionQuery): DataResponse
    {
        $updatedIds = (app()->orderChannel->updateBatch($request))->pluck('id');

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
                app()->orderChannel->destroyBatch($request)
            )
        );
    }
}
