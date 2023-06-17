<?php

namespace App\Http\Controllers\Api;

use App\Http\Requests\Return_\StoreBatchRequest;
use App\Http\Requests\Return_\DestroyBatchRequest;
use App\Http\Requests\Return_\UpdateBatchRequest;
use App\Http\Requests\Return_\ReceiveBatchRequest;
use App\Http\Requests\Return_\FilterRequest;
use App\JsonApi\V1\ReturnItems\ReturnItemSchema;
use App\JsonApi\V1\Returns\ReturnSchema;
use App\JsonApi\V1\Revisions\RevisionSchema;
use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Resources\Json\ResourceCollection;
use App\Models\Return_;
use App\Models\Order;
use App\Models\UserRole;
use App\Models\ReturnItem;
use App\Http\Controllers\ApiController;
use LaravelJsonApi\Core\Responses\DataResponse;
use LaravelJsonApi\Laravel\Http\Requests\AnonymousCollectionQuery;

/**
 * Class ReturnController
 * @package App\Http\Controllers\Api
 * @group Returns
 */
class ReturnController extends ApiController
{
    public function __construct()
    {
        $this->authorizeResource(Return_::class);

        foreach ($this->middleware as $key => $value) {
            if (isset($value['middleware']) && $value['middleware'] == 'can:view,return_') {
                $this->middleware[$key]['middleware'] = 'can:view,return';
                break;
            }
        }
    }

    /**
     * @param ReturnSchema $schema
     * @param AnonymousCollectionQuery $request
     * @return DataResponse
     */
    public function index(ReturnSchema $schema, AnonymousCollectionQuery $request): DataResponse
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
     * @param ReturnSchema $schema
     * @param StoreBatchRequest $request
     * @param AnonymousCollectionQuery $collectionQuery
     * @return DataResponse
     */
    public function store(ReturnSchema $schema, StoreBatchRequest $request, AnonymousCollectionQuery $collectionQuery): DataResponse
    {
        $storedIds = (app()->return->storeBatch($request))->pluck('id');

        $models = $schema
            ->repository()
            ->queryAll()
            ->withRequest($collectionQuery)
            ->firstOrPaginate($collectionQuery->page());

        $models = $models->whereIn('id', $storedIds);

        return new DataResponse($models);
    }

    /**
     * @param ReturnSchema $schema
     * @param UpdateBatchRequest $request
     * @param AnonymousCollectionQuery $collectionQuery
     * @return DataResponse
     */
    public function update(ReturnSchema $schema, UpdateBatchRequest $request, AnonymousCollectionQuery $collectionQuery): DataResponse
    {
        $updatedIds = (app()->return->updateBatch($request))->pluck('id');

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
     * @param  DestroyBatchRequest $request
     * @return JsonResponse
     */
    public function destroy(DestroyBatchRequest $request): JsonResponse
    {
        return response()->json(
            new ResourceCollection(
                app()->return->destroyBatch($request)
            )
        );
    }

    /**
     * @param ReturnItemSchema $schema
     * @param AnonymousCollectionQuery $collectionQuery
     * @param ReceiveBatchRequest $request
     * @param Return_ $return
     * @return DataResponse
     * @throws AuthorizationException
     */
    public function receive(ReturnItemSchema $schema, AnonymousCollectionQuery $collectionQuery, ReceiveBatchRequest $request, Return_ $return): DataResponse
    {
        $this->authorize('batchReceive', $return);

        $receivedIds =  (app()->return->receiveBatch($request, $return)->pluck('id'));
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
     * @param Return_ $return
     * @return DataResponse
     * @throws AuthorizationException
     */
    public function history(RevisionSchema $schema, AnonymousCollectionQuery $collectionQuery, Return_ $return): DataResponse
    {
        $this->authorize('history', $return);

        $revisionIds = (app()->return->history($return)->pluck('id'));

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
     * @param ReturnItem $returnItem
     * @return DataResponse
     * @throws AuthorizationException
     */
    public function itemHistory(RevisionSchema $schema, AnonymousCollectionQuery $collectionQuery, ReturnItem $returnItem): DataResponse
    {
        $this->authorize('itemHistory', $returnItem->return_);

        $revisionIds = (app()->return->history($returnItem)->pluck('id'));

        $models = $schema
            ->repository()
            ->queryAll()
            ->withRequest($collectionQuery)
            ->firstOrPaginate($collectionQuery->page());

        $models = $models->whereIn('id', $revisionIds);

        return new DataResponse($models);
    }

    /**
     * @param ReturnSchema $schema
     * @param AnonymousCollectionQuery $collectionQuery
     * @param FilterRequest $request
     * @return DataResponse
     */
    public function filter(ReturnSchema $schema, AnonymousCollectionQuery $collectionQuery, FilterRequest $request): DataResponse
    {
        $user = auth()->user();

        $orderIds = [];

        if ($user->user_role_id != UserRole::ROLE_ADMINISTRATOR) {
            $customerIds = $user->customers()->pluck('customer_user.customer_id')->unique()->toArray();

            $orderIds = Order::whereIn('customer_id', $customerIds)->pluck('id')->unique()->toArray();
        }

        $returnIds = (app()->return->filter($request, $orderIds)->pluck('id'));

        $models = $schema
            ->repository()
            ->queryAll()
            ->withRequest($collectionQuery)
            ->firstOrPaginate($collectionQuery->page());

        $models = $models->whereIn('id', $returnIds);

        return new DataResponse($models);
    }
}
