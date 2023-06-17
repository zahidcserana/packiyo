<?php

namespace App\Http\Controllers\Api;

use App\Http\Requests\Customer\DestroyBatchRequest;
use App\Http\Requests\Customer\StoreBatchRequest;
use App\Http\Requests\Customer\UpdateBatchRequest;
use App\Http\Requests\Customer\UpdateUsersRequest;
use App\JsonApi\V1\Customers\CustomerSchema;
use App\JsonApi\V1\Products\ProductSchema;
use App\JsonApi\V1\Tasks\TaskSchema;
use App\JsonApi\V1\Users\UserSchema;
use App\JsonApi\V1\Warehouses\WarehouseSchema;
use App\Models\UserRole;
use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Resources\Json\ResourceCollection;
use App\Http\Resources\CustomerCollection;
use App\Http\Resources\TaskCollection;
use App\Http\Resources\ProductCollection;
use App\Models\Customer;
use App\Models\CustomerUser;
use App\Models\User;
use App\Http\Controllers\ApiController;
use Illuminate\Http\Response;
use LaravelJsonApi\Core\Responses\DataResponse;
use LaravelJsonApi\Laravel\Http\Controllers\Actions\FetchMany;
use LaravelJsonApi\Laravel\Http\Controllers\Actions\FetchOne;
use LaravelJsonApi\Laravel\Http\Requests\AnonymousCollectionQuery;

/**
 * Class CustomerController
 * @package App\Http\Controllers\Api
 * @group Customers
 */
class CustomerController extends ApiController
{
    use FetchOne;
    use FetchMany;

    public function __construct()
    {
        $this->authorizeResource(Customer::class);
    }

    public function index(CustomerSchema $schema, AnonymousCollectionQuery $request): DataResponse
    {
        $user = auth()->user();

        $models = $schema
            ->repository()
            ->queryAll()
            ->withRequest($request)
            ->query();

        if ($user->user_role_id != UserRole::ROLE_ADMINISTRATOR) {
            $customerIds = $user->customers()->pluck('customer_user.customer_id')->unique()->toArray();
            $models = $models->whereIn('id', $customerIds);
        }

        if ($request->page()) {
            $models = $models->paginate($request->page());
        } else {
            $models = $models->get();
        }

        return new DataResponse($models);
    }

    /**
     * @param UserSchema $schema
     * @param AnonymousCollectionQuery $collectionQuery
     * @param Customer $customer
     * @return DataResponse
     * @throws AuthorizationException
     */
    public function listUsers(UserSchema $schema, AnonymousCollectionQuery $collectionQuery, Customer $customer): DataResponse
    {
        $this->authorize('users', $customer);

        $userIds = ( CustomerUser::where('customer_id', $customer['id'])->get()->pluck('user_id'));

        $models = $schema
            ->repository()
            ->queryAll()
            ->withRequest($collectionQuery)
            ->firstOrPaginate($collectionQuery->page());

        $models = $models->whereIn('id', $userIds);

        return new DataResponse($models);
    }

    /**
     * @param CustomerSchema $schema
     * @param AnonymousCollectionQuery $collectionQuery
     * @param UpdateUsersRequest $request
     * @param Customer $customer
     * @return DataResponse
     * @throws AuthorizationException
     */
    public function updateUsers(CustomerSchema $schema, AnonymousCollectionQuery $collectionQuery, UpdateUsersRequest $request, Customer $customer): DataResponse
    {
        $this->authorize('updateUsers', $customer);

        $customerIds = (app()->customer->updateUsers($request, $customer)->pluck('id'));

        $models = $schema
            ->repository()
            ->queryAll()
            ->withRequest($collectionQuery)
            ->firstOrPaginate($collectionQuery->page());

        $models = $models->whereIn('id', $customerIds);

        return new DataResponse($models);
    }

    /**
     * @param Customer $customer
     * @param User $user
     * @return JsonResponse
     * @throws AuthorizationException
     */
    public function detachUser(Customer $customer, User $user): JsonResponse
    {
        $this->authorize('updateUsers', $customer);

        return response()->json(
            app()->customer->detachUser($customer, $user)
        );
    }

    /**
     * @param CustomerSchema $schema
     * @param StoreBatchRequest $request
     * @param AnonymousCollectionQuery $collectionQuery
     * @return DataResponse
     */
    public function store(CustomerSchema $schema, StoreBatchRequest $request, AnonymousCollectionQuery $collectionQuery): DataResponse
    {
        $storedIds = (app()->customer->storeBatch($request))->pluck('id');

        $models = $schema
            ->repository()
            ->queryAll()
            ->withRequest($collectionQuery)
            ->firstOrPaginate($collectionQuery->page());

        $models = $models->whereIn('id', $storedIds);

        return new DataResponse($models);
    }

    /**
     * @param CustomerSchema $schema
     * @param UpdateBatchRequest $request
     * @param AnonymousCollectionQuery $collectionQuery
     * @return DataResponse
     */
    public function update(CustomerSchema $schema, UpdateBatchRequest $request, AnonymousCollectionQuery $collectionQuery): DataResponse
    {
        $updatedIds = (app()->customer->updateBatch($request))->pluck('id');

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
                app()->customer->destroyBatch($request)
            )
        );
    }

    /**
     * @param WarehouseSchema $schema
     * @param AnonymousCollectionQuery $collectionQuery
     * @param Customer $customer
     * @return DataResponse
     * @throws AuthorizationException
     */
    public function warehouses(WarehouseSchema $schema, AnonymousCollectionQuery $collectionQuery, Customer $customer): DataResponse
    {
        $this->authorize('warehouses', $customer);

        $warehouseIds = (app()->warehouse->getCustomerWarehouses($customer)->pluck('id'));

        $models = $schema
            ->repository()
            ->queryAll()
            ->withRequest($collectionQuery)
            ->firstOrPaginate($collectionQuery->page());

        $models = $models->whereIn('id', $warehouseIds);

        return new DataResponse($models);
    }

    /**
     * @param UserSchema $schema
     * @param AnonymousCollectionQuery $collectionQuery
     * @param Customer $customer
     * @return Response|DataResponse
     * @throws AuthorizationException
     */
    public function users(UserSchema $schema, AnonymousCollectionQuery $collectionQuery, Customer $customer): Response|DataResponse
    {
        $this->authorize('users', $customer);
        $userIds = (app()->user->getCustomerUsers($customer)->pluck('id'));

        $models = $schema
            ->repository()
            ->queryAll()
            ->withRequest($collectionQuery)
            ->firstOrPaginate($collectionQuery->page());

        $models = $models->whereIn('id', $userIds);

        return new DataResponse($models);
    }

    /**
     * @param TaskSchema $schema
     * @param AnonymousCollectionQuery $collectionQuery
     * @param Customer $customer
     * @return Response|DataResponse
     * @throws AuthorizationException
     */
    public function tasks(TaskSchema $schema, AnonymousCollectionQuery $collectionQuery, Customer $customer): Response|DataResponse
    {
        $this->authorize('tasks', $customer);

        $taskIds = (app()->task->getCustomerTasks($customer)->pluck('id'));

        $models = $schema
            ->repository()
            ->queryAll()
            ->withRequest($collectionQuery)
            ->firstOrPaginate($collectionQuery->page());

        $models = $models->whereIn('id', $taskIds);

        return new DataResponse($models);
    }

    /**
     * @param ProductSchema $schema
     * @param AnonymousCollectionQuery $collectionQuery
     * @param Customer $customer
     * @return Response|DataResponse
     * @throws AuthorizationException
     */
    public function products(ProductSchema $schema, AnonymousCollectionQuery $collectionQuery, Customer $customer): Response|DataResponse
    {
        $this->authorize('products', $customer);

        $productIds = (app()->product->getCustomerProduct($customer)->pluck('id'));

        $models = $schema
            ->repository()
            ->queryAll()
            ->withRequest($collectionQuery)
            ->firstOrPaginate($collectionQuery->page());

        $models = $models->whereIn('id', $productIds);

        return new DataResponse($models);
    }
}
