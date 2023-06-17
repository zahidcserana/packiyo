<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\ApiController;
use App\Http\Requests\AccessTokenRequest;
use App\Http\Requests\User\DestroyBatchRequest;
use App\Http\Requests\User\StoreBatchRequest;
use App\Http\Requests\User\UpdateBatchRequest;
use App\Http\Resources\CustomerResource;
use App\JsonApi\V1\AccessTokens\AccessTokenSchema;
use App\JsonApi\V1\Customers\CustomerSchema;
use App\JsonApi\V1\Users\UserSchema;
use App\JsonApi\V1\Webhooks\WebhookSchema;
use App\Models\User;
use App\Models\CustomerUser;
use App\Models\UserRole;
use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Resources\Json\ResourceCollection;
use Illuminate\Http\Request;
use App\Http\Resources\CustomerCollection;
use LaravelJsonApi\Core\Responses\DataResponse;
use LaravelJsonApi\Laravel\Http\Controllers\Actions\FetchOne;
use LaravelJsonApi\Laravel\Http\Requests\AnonymousCollectionQuery;

/**
 * Class UserController
 * @package App\Http\Controllers\Api
 * @group Users
 */
class UserController extends ApiController
{
    use FetchOne;

    public function __construct()
    {
        $this->authorizeResource(User::class);
    }

    /**
     * Display a listing of the resource.
     *
     * @return DataResponse
     */
    public function index(UserSchema $schema, AnonymousCollectionQuery $request)
    {
        $models = $schema
            ->repository()
            ->queryAll()
            ->withRequest($request)
            ->firstOrPaginate($request->page());

        $user = auth()->user();

        if ($user->user_role_id != UserRole::ROLE_ADMINISTRATOR) {
            $customerIds = $user->customers->pluck('id');
            $userIds = CustomerUser::whereIn('customer_id', $customerIds)->pluck('user_id')->unique()->toArray();
            $models = $models->whereIn('id', $userIds);
        }

        return new DataResponse($models);
    }

    /**
     * @param UserSchema $schema
     * @param StoreBatchRequest $request
     * @param AnonymousCollectionQuery $collectionQuery
     * @return DataResponse
     */
    public function store(UserSchema $schema, StoreBatchRequest $request, AnonymousCollectionQuery $collectionQuery)
    {
        $storedIds = (app()->user->storeBatch($request))->pluck('id');

        $models = $schema
            ->repository()
            ->queryAll()
            ->withRequest($collectionQuery)
            ->firstOrPaginate($collectionQuery->page());

        $models = $models->whereIn('id', $storedIds);

        return new DataResponse($models);
    }

    /**
     * @param UserSchema $schema
     * @param UpdateBatchRequest $request
     * @param AnonymousCollectionQuery $collectionQuery
     * @return DataResponse
     */
    public function update(UserSchema $schema, UpdateBatchRequest $request, AnonymousCollectionQuery $collectionQuery)
    {
        $updatedIds = (app()->user->storeBatch($request))->pluck('id');

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
        $users = app()->user->destroyBatch($request);

        $users = $users->map(function ($item) {
            return array_only($item, ['email']);
        });

        return response()->json(
            new ResourceCollection($users)
        );
    }

    /**
     * @param CustomerSchema $schema
     * @param AnonymousCollectionQuery $collectionQuery
     * @param User $user
     * @return CustomerCollection|DataResponse
     * @throws AuthorizationException
     */
    public function customers(CustomerSchema $schema, AnonymousCollectionQuery $collectionQuery, User $user): DataResponse //CustomerCollection|DataResponse
    {
        $this->authorize('customers', $user);

        $customerIds = (app()->customer->getUserCustomers($user)->pluck('id'));

        $models = $schema
            ->repository()
            ->queryAll()
            ->withRequest($collectionQuery)
            ->firstOrPaginate($collectionQuery->page());

        $models = $models->whereIn('id', $customerIds);

        return new DataResponse($models);
    }

    /**
     * @param WebhookSchema $schema
     * @param AnonymousCollectionQuery $collectionQuery
     * @param User $user
     * @return DataResponse
     * @throws AuthorizationException
     */
    public function webhooks(WebhookSchema $schema, AnonymousCollectionQuery $collectionQuery, User $user): DataResponse
    {
        $this->authorize('webhooks', $user);

        $webhookIds = (app()->webhook->getUserWebhooks($user)->pluck('id'));

        $models = $schema
            ->repository()
            ->queryAll()
            ->withRequest($collectionQuery)
            ->firstOrPaginate($collectionQuery->page());

        $models = $models->whereIn('id', $webhookIds);

        return new DataResponse($models);
    }
}
