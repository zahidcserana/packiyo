<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\ApiController;
use App\Http\Requests\CycleCountBatch\CloseCountingTaskRequest;
use App\Http\Requests\CycleCountBatch\CountRequest;
use App\Http\Requests\CycleCountBatch\CycleCountBatchRequest;
use App\Http\Requests\CycleCountBatch\PickRequest;
use App\JsonApi\V1\CycleCountBatches\CycleCountBatchSchema;
use LaravelJsonApi\Core\Responses\DataResponse;
use LaravelJsonApi\Laravel\Http\Requests\AnonymousCollectionQuery;

class CycleCountBatchController extends ApiController
{
    public function availableCountingBatch(CycleCountBatchSchema $schema, AnonymousCollectionQuery $collectionQuery, CycleCountBatchRequest $request): DataResponse
    {
        $cycleCountBatch = (app()->cycleCountBatch->availableBatch($request));
        return new DataResponse($cycleCountBatch);
    }

    public function closeCountingTask(CycleCountBatchSchema $schema, AnonymousCollectionQuery $collectionQuery, CloseCountingTaskRequest $request): DataResponse
    {
        $cycleCountBatch = (app()->cycleCountBatch->closeCountingTask($request));
        return new DataResponse($cycleCountBatch);
    }

    public function pick(CycleCountBatchSchema $schema, AnonymousCollectionQuery $collectionQuery, PickRequest $request): DataResponse
    {
        $cycleCountBatch = (app()->cycleCountBatch->pick($request));
        return new DataResponse($cycleCountBatch);
    }

    public function count(CycleCountBatchSchema $schema, AnonymousCollectionQuery $collectionQuery, CountRequest $request): DataResponse
    {
        $cycleCountBatch = (app()->cycleCountBatch->count($request));
        return new DataResponse($cycleCountBatch);
    }
}
