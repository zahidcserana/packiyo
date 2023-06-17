<?php

namespace App\Http\Controllers\Api;

use App\Http\Resources\InventoryLogResource;
use App\JsonApi\V1\InventoryLogs\InventoryLogSchema;
use App\Models\InventoryLog;
use App\Models\UserRole;
use App\Http\Controllers\ApiController;
use LaravelJsonApi\Core\Responses\DataResponse;
use LaravelJsonApi\Laravel\Http\Controllers\Actions\FetchOne;
use LaravelJsonApi\Laravel\Http\Requests\AnonymousCollectionQuery;

/**
 * Class InventoryLogController
 * @package App\Http\Controllers\Api
 * @group Inventory Logs
 */
class InventoryLogController extends ApiController
{
    use FetchOne;

    public function __construct()
    {
        $this->authorizeResource(InventoryLog::class);
    }

    /**
     * @param InventoryLogSchema $schema
     * @param AnonymousCollectionQuery $request
     * @return DataResponse
     */
     public function index(InventoryLogSchema $schema, AnonymousCollectionQuery $request): DataResponse
     {
         $models = $schema
             ->repository()
             ->queryAll()
             ->withRequest($request)
             ->firstOrPaginate($request->page());

         $user = auth()->user();

         if ($user->user_role_id != UserRole::ROLE_ADMINISTRATOR) {
             $userIds = app()->user->getAllCustomerUserIds($user);
             $models = $models->whereIn('customer_id', array_unique($userIds));
         }

         return new DataResponse($models);
     }
}
