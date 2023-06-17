<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\ApiController;
use App\Http\Requests\EasypostCredential\DestroyBatchRequest;
use App\Http\Requests\EasypostCredential\StoreBatchRequest;
use App\Http\Requests\EasypostCredential\UpdateBatchRequest;
use App\JsonApi\V1\EasypostCredentials\EasypostCredentialSchema;
use App\Models\EasypostCredential;
use App\Models\UserRole;
use Illuminate\Http\JsonResponse;
use LaravelJsonApi\Core\Responses\DataResponse;
use LaravelJsonApi\Laravel\Http\Controllers\Actions\FetchOne;
use LaravelJsonApi\Laravel\Http\Requests\AnonymousCollectionQuery;

class EasypostCredentialController extends ApiController
{
    use FetchOne;

    public function __construct()
    {
        $this->authorizeResource(EasypostCredential::class);
    }

    /**
     * @param EasypostCredentialSchema $schema
     * @param AnonymousCollectionQuery $request
     * @return DataResponse
     */
    public function index(EasypostCredentialSchema $schema, AnonymousCollectionQuery $request): DataResponse
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
     * @param EasypostCredentialSchema $schema
     * @param StoreBatchRequest $request
     * @param AnonymousCollectionQuery $collectionQuery
     * @return DataResponse
     */
    public function store(EasypostCredentialSchema $schema, StoreBatchRequest $request, AnonymousCollectionQuery $collectionQuery): DataResponse
    {
        $storedIds = (app()->easypostCredential->storeBatch($request))->pluck('id');

        $models = $schema
            ->repository()
            ->queryAll()
            ->withRequest($collectionQuery)
            ->firstOrPaginate($collectionQuery->page());

        $models = $models->whereIn('id', $storedIds);

        return new DataResponse($models);
    }

    /**
     * @param EasypostCredentialSchema $schema
     * @param UpdateBatchRequest $request
     * @param AnonymousCollectionQuery $collectionQuery
     * @return DataResponse
     */
    public function update(EasypostCredentialSchema $schema, UpdateBatchRequest $request, AnonymousCollectionQuery $collectionQuery): DataResponse
    {
        $updatedIds = (app()->easypostCredential->updateBatch($request))->pluck('id');

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
            app()->easypostCredential->destroyBatch($request)
        );
    }
}
