<?php

namespace App\Http\Controllers\Api;

use App\Http\Requests\WebshipperCredential\StoreBatchRequest;
use App\Http\Requests\WebshipperCredential\UpdateBatchRequest;
use App\Http\Requests\WebshipperCredential\DestroyBatchRequest;
use App\JsonApi\V1\WebshipperCredentials\WebshipperCredentialSchema;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Resources\Json\ResourceCollection;
use App\Models\WebshipperCredential;
use App\Models\UserRole;
use App\Http\Controllers\ApiController;
use LaravelJsonApi\Core\Responses\DataResponse;
use LaravelJsonApi\Laravel\Http\Controllers\Actions\FetchOne;
use LaravelJsonApi\Laravel\Http\Requests\AnonymousCollectionQuery;

class WebshipperCredentialController extends ApiController
{
    use FetchOne;

    public function __construct()
    {
        $this->authorizeResource(WebshipperCredential::class);
    }

    /**
     * @param WebshipperCredentialSchema $schema
     * @param AnonymousCollectionQuery $request
     * @return DataResponse
     */
    public function index(WebshipperCredentialSchema $schema, AnonymousCollectionQuery $request): DataResponse
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
     * @param WebshipperCredentialSchema $schema
     * @param StoreBatchRequest $request
     * @param AnonymousCollectionQuery $collectionQuery
     * @return DataResponse
     */
    public function store(WebshipperCredentialSchema $schema, StoreBatchRequest $request, AnonymousCollectionQuery $collectionQuery): DataResponse
    {
        $storedIds = (app()->webshipperCredential->storeBatch($request))->pluck('id');

        $models = $schema
            ->repository()
            ->queryAll()
            ->withRequest($collectionQuery)
            ->firstOrPaginate($collectionQuery->page());

        $models = $models->whereIn('id', $storedIds);

        return new DataResponse($models);
    }

    /**
     * @param WebshipperCredentialSchema $schema
     * @param UpdateBatchRequest $request
     * @param AnonymousCollectionQuery $collectionQuery
     * @return DataResponse
     */
    public function update(WebshipperCredentialSchema $schema, UpdateBatchRequest $request, AnonymousCollectionQuery $collectionQuery): DataResponse
    {
        $updatedIds = (app()->webshipperCredential->updateBatch($request))->pluck('id');

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
                app()->webshipperCredential->destroyBatch($request)
            )
        );
    }
}
