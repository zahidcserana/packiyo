<?php

namespace App\Http\Controllers\Api;

use App\Components\InventoryLogComponent;
use App\Http\Controllers\ApiController;
use App\Http\Requests\Product\ChangeLocationQuantityRequest;
use App\Http\Requests\Product\DestroyBatchRequest;
use App\Http\Requests\Product\StoreBatchRequest;
use App\Http\Requests\Product\TransferRequest;
use App\Http\Requests\Product\UpdateBatchRequest;
use App\Http\Requests\Product\FilterRequest;
use App\Http\Requests\Product\UpdateRequest;
use App\JsonApi\V1\Products\ProductSchema;
use App\JsonApi\V1\Revisions\RevisionSchema;
use App\Models\Customer;
use App\Models\Location;
use App\Models\LocationProduct;
use App\Models\UserRole;
use App\Models\Product;
use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Resources\Json\ResourceCollection;
use LaravelJsonApi\Contracts\Routing\Route;
use LaravelJsonApi\Core\Responses\DataResponse;
use LaravelJsonApi\Laravel\Http\Controllers\Actions\FetchOne;
use LaravelJsonApi\Laravel\Http\Requests\AnonymousCollectionQuery;

/**
 * Class ProductController
 * @package App\Http\Controllers\Api
 * @group Products
 */
class ProductController extends ApiController
{
    use FetchOne;

    public function __construct()
    {
        $this->authorizeResource(Product::class);
    }

    /**
     * @param ProductSchema $schema
     * @param AnonymousCollectionQuery $request
     * @return DataResponse
     */
    public function index(ProductSchema $schema, AnonymousCollectionQuery $request): DataResponse
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

        if ($request->location_id) {
            $productIds = LocationProduct::where('location_id', $request->location_id)->pluck('product_id')->toArray();
            $models = $models->whereIntegerInRaw('id', $productIds);
        }

        $models = $models->whereIntegerInRaw('customer_id', $customerIds);

        if ($request->page()) {
            $models = $models->paginate($request->page());
        } else {
            $models = $models->get();
        }

        return new DataResponse($models);
    }

    /**
     * @param ProductSchema $schema
     * @param StoreBatchRequest $request
     * @param AnonymousCollectionQuery $collectionQuery
     * @return DataResponse
     */
    public function store(ProductSchema $schema, StoreBatchRequest $request, AnonymousCollectionQuery $collectionQuery): DataResponse
    {
        $storedIds = (app()->product->storeBatch($request))->pluck('id');

        $models = $schema
            ->repository()
            ->queryAll()
            ->withRequest($collectionQuery)
            ->firstOrPaginate($collectionQuery->page());

        $models = $models->whereIn('id', $storedIds);

        return new DataResponse($models);
    }

    /**
     * @param ProductSchema $schema
     * @param UpdateRequest $request
     * @param AnonymousCollectionQuery $collectionQuery
     * @return DataResponse
     */
    public function update(Route $route, ProductSchema $schema, UpdateRequest $request, AnonymousCollectionQuery $collectionQuery): DataResponse
    {
        app()->product->update($request, $route->model());

        $models = $schema
            ->repository()
            ->find($route->resourceId());

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
                app()->product->destroyBatch($request)
            )
        );
    }

    /**
     * @param RevisionSchema $schema
     * @param AnonymousCollectionQuery $collectionQuery
     * @param Product $product
     * @return DataResponse
     * @throws AuthorizationException
     */
    public function history(RevisionSchema $schema, AnonymousCollectionQuery $collectionQuery, Product $product): DataResponse
    {
        $this->authorize('history', $product);
        $revisionIds = (app()->product->history($product)->pluck('id'));

        $models = $schema
            ->repository()
            ->queryAll()
            ->withRequest($collectionQuery)
            ->firstOrPaginate($collectionQuery->page());

        $models = $models->whereIn('id', $revisionIds);

        return new DataResponse($models);
    }

    /**
     * @param ProductSchema $schema
     * @param AnonymousCollectionQuery $collectionQuery
     * @param FilterRequest $request
     * @return DataResponse
     */
    public function filter(ProductSchema $schema, AnonymousCollectionQuery $collectionQuery, FilterRequest $request): DataResponse
    {
        $user = auth()->user();

        $customerIds = [];

        if ($user->user_role_id != UserRole::ROLE_ADMINISTRATOR) {
            $customerIds = $user->customers()->pluck('customer_user.customer_id')->unique()->toArray();
        }

        $productIds = (app()->product->filter($request, $customerIds)->pluck('id'));

        $models = $schema
            ->repository()
            ->queryAll()
            ->withRequest($collectionQuery)
            ->firstOrPaginate($collectionQuery->page());

        $models = $models->whereIn('id', $productIds);

        return new DataResponse($models);
    }

    public function transfer(TransferRequest $request, Product $product)
    {
        $location = Location::where('id', $request['to_location_id'])->first();
        $fromLocation = Location::where('id', $request['from_location_id'])->first();

        return response()->json(['success' =>  app('inventoryLog')->adjustInventory($location, $product, $request['quantity'], InventoryLogComponent::OPERATION_TYPE_TRANSFER, $fromLocation)]);
    }

    public function changeLocationQuantity(Route $route, ProductSchema $schema, ChangeLocationQuantityRequest $request, AnonymousCollectionQuery $collectionQuery): DataResponse
    {
        $product = $route->model();

        $this->authorize('changeLocationQuantity', $product);

        app('product')->changeLocationQuantity($request, $product);

        $models = $schema
            ->repository()
            ->find($route->resourceId());

        return new DataResponse($models);
    }
}
