<?php

namespace App\Http\Controllers\Api;

use App\Http\Requests\Task\StoreBatchRequest;
use App\Http\Requests\Task\UpdateBatchRequest;
use App\Http\Requests\Task\DestroyBatchRequest;
use App\Http\Resources\TaskResource;
use App\Http\Resources\TaskCollection;
use App\Http\Resources\TaskTableResource;
use App\JsonApi\V1\Tasks\TaskSchema;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\ResourceCollection;
use App\Models\Task;
use App\Models\UserRole;
use App\Http\Controllers\ApiController;
use LaravelJsonApi\Core\Responses\DataResponse;
use LaravelJsonApi\Laravel\Http\Controllers\Actions\FetchOne;
use LaravelJsonApi\Laravel\Http\Requests\AnonymousCollectionQuery;

/**
 * Class TaskController
 * @package App\Http\Controllers\Api
 * @group Tasks
 */
class TaskController extends ApiController
{
    use FetchOne;

    public function __construct()
    {
        $this->authorizeResource(Task::class);
    }

    /**
     * @param TaskSchema $schema
     * @param AnonymousCollectionQuery $request
     * @return DataResponse
     */
    public function index(TaskSchema $schema, AnonymousCollectionQuery $request): DataResponse
    {
        $models = $schema
            ->repository()
            ->queryAll()
            ->withRequest($request)
            ->firstOrPaginate($request->page());

        $user = auth()->user();

        if ($user->user_role_id != UserRole::ROLE_ADMINISTRATOR) {
            $taskIds = app()->task->getUserTasks($user)->pluck('id')->unique()->toArray();
            $models = $models->whereIn('customer_id', $taskIds);
        }

        return new DataResponse($models);
    }

    /**
     * @param TaskSchema $schema
     * @param StoreBatchRequest $request
     * @param AnonymousCollectionQuery $collectionQuery
     * @return DataResponse
     */
    public function store(TaskSchema $schema, StoreBatchRequest $request, AnonymousCollectionQuery $collectionQuery): DataResponse
    {
        $storedIds = (app()->task->storeBatch($request))->pluck('id');

        $models = $schema
            ->repository()
            ->queryAll()
            ->withRequest($collectionQuery)
            ->firstOrPaginate($collectionQuery->page());

        $models = $models->whereIn('id', $storedIds);

        return new DataResponse($models);
    }

    /**
     * @param TaskSchema $schema
     * @param UpdateBatchRequest $request
     * @param AnonymousCollectionQuery $collectionQuery
     * @return DataResponse
     */
    public function update(TaskSchema $schema, UpdateBatchRequest $request, AnonymousCollectionQuery $collectionQuery): DataResponse
    {
        $updatedIds = (app()->task->updateBatch($request))->pluck('id');

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
                app()->task->destroyBatch($request)
            )
        );
    }
}
