<?php

namespace App\Http\Controllers\Api;

use App\Http\Requests\Tote\DestroyBatchRequest;
use App\Http\Requests\Tote\PickOrderItemsByBarcodeRequest;
use App\Http\Requests\Tote\PickOrderItemsRequest;
use App\Http\Requests\Tote\PickOrderItemsWithToteRequest;
use App\Http\Requests\Tote\StoreBatchRequest;
use App\Http\Requests\Tote\UpdateBatchRequest;
use App\JsonApi\V1\ToteOrderItems\PlacedToteOrderItemSchema;
use App\JsonApi\V1\Totes\ToteSchema;
use App\Models\Customer;
use App\Models\Order;
use App\Models\PickingBatch;
use App\Models\Tote;
use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Resources\Json\ResourceCollection;
use App\Http\Controllers\ApiController;
use Illuminate\Support\Facades\Log;
use LaravelJsonApi\Core\Responses\DataResponse;
use LaravelJsonApi\Laravel\Http\Controllers\Actions\FetchOne;
use LaravelJsonApi\Laravel\Http\Requests\AnonymousCollectionQuery;

/**
 * Class ToteController
 * @package App\Http\Controllers\Api
 * @group Totes
 */
class ToteController extends ApiController
{
    use FetchOne;

    public function __construct()
    {
        $this->authorizeResource(Tote::class);
    }

    /**
     * Display a listing of the resource.
     *
     * @param ToteSchema $schema
     * @param AnonymousCollectionQuery $request
     * @return DataResponse
     */
    public function index(ToteSchema $schema, AnonymousCollectionQuery $request): DataResponse
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

        $usedTotes = [];

        $models->with('warehouse')
            ->whereHas('warehouse', function($warehouse) use ($customerIds) {
                $warehouse->whereIntegerInRaw('customer_id', $customerIds);
            })->with('placedToteOrderItems.orderItem');

        if ($request->picking_batch_id) {
            if ($pickingBatch = PickingBatch::with('pickingBatchItems.toteOrderItems')->find($request->picking_batch_id)) {
                foreach ($pickingBatch->pickingBatchItems as $pickingBatchItem) {
                    foreach ($pickingBatchItem->toteOrderItems as $toteOrderItem) {
                        $usedTotes[] = $toteOrderItem->tote_id;
                    }
                }
            }
            $models->whereIntegerInRaw('id', $usedTotes);
        } else {
            $models->whereDoesntHave('placedToteOrderItems');
        }

        if ($page = $request->page()) {
            $models = $models->paginate($page);
        } else {
            $models = $models->get();
        }

        return new DataResponse($models);
    }
    /**
     * @param ToteSchema $schema
     * @param StoreBatchRequest $request
     * @param AnonymousCollectionQuery $collectionQuery
     * @return DataResponse
     */
    public function store(ToteSchema $schema, StoreBatchRequest $request, AnonymousCollectionQuery $collectionQuery): DataResponse
    {
        $storedIds = (app()->tote->storeBatch($request))->pluck('id');

        $models = $schema
            ->repository()
            ->queryAll()
            ->withRequest($collectionQuery)
            ->firstOrPaginate($collectionQuery->page());

        $models = $models->whereIntegerInRaw('id', $storedIds);

        return new DataResponse($models);
    }

    /**
     * @param ToteSchema $schema
     * @param UpdateBatchRequest $request
     * @param AnonymousCollectionQuery $collectionQuery
     * @return DataResponse
     */
    public function update(ToteSchema $schema, UpdateBatchRequest $request, AnonymousCollectionQuery $collectionQuery): DataResponse
    {
        $updatedIds = (app()->tote->storeBatch($request))->pluck('id');

        $models = $schema
            ->repository()
            ->queryAll()
            ->withRequest($collectionQuery)
            ->firstOrPaginate($collectionQuery->page());

        $models = $models->whereIntegerInRaw('id', $updatedIds);

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
                app()->tote->destroyBatch($request)
            )
        );
    }

    /**
     * @param PlacedToteOrderItemSchema $schema
     * @param Tote $tote
     * @param AnonymousCollectionQuery $collectionQuery
     * @return DataResponse
     * @throws AuthorizationException
     */
    public function toteOrderItems(PlacedToteOrderItemSchema $schema, Tote $tote, AnonymousCollectionQuery $collectionQuery): DataResponse
    {
        $this->authorize('view', $tote);

        $toteItemIds = (app()->tote->toteItems($tote))->pluck('id');

        $models = $schema
            ->repository()
            ->queryAll()
            ->withRequest($collectionQuery)
            ->firstOrPaginate($collectionQuery->page());

        $models = $models->whereIntegerInRaw('id', $toteItemIds);

        return new DataResponse($models);
    }

    /**
     * @param PlacedToteOrderItemSchema $schema
     * @param Tote $tote
     * @param PickOrderItemsRequest $request
     * @param AnonymousCollectionQuery $collectionQuery
     * @return DataResponse
     * @throws AuthorizationException
     */
    public function pickOrderItems(PlacedToteOrderItemSchema $schema, Tote $tote, PickOrderItemsRequest $request, AnonymousCollectionQuery $collectionQuery): DataResponse
    {
        $this->authorize('view', $tote);

        $toteItemIds = (app()->tote->pickOrderItems($request, $tote))->pluck('id');

        $models = $schema
            ->repository()
            ->queryAll()
            ->withRequest($collectionQuery)
            ->firstOrPaginate($collectionQuery->page());

        $models = $models->whereIntegerInRaw('id', $toteItemIds);

        return new DataResponse($models);
    }

    /**
     * @param Tote $tote
     * @return DataResponse
     * @throws AuthorizationException
     */
    public function emptyTote(Tote $tote): DataResponse
    {
        $this->authorize('view', $tote);

        app()->tote->clearTote($tote);

        return new DataResponse($tote);
    }
}
