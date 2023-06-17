<?php

namespace App\Http\Controllers\Api;

use App\JsonApi\V1\Tags\TagSchema;
use App\Models\Customer;
use App\Models\Tag;
use App\Http\Controllers\ApiController;
use LaravelJsonApi\Core\Responses\DataResponse;
use LaravelJsonApi\Laravel\Http\Controllers\Actions\FetchOne;
use LaravelJsonApi\Laravel\Http\Requests\AnonymousCollectionQuery;

/**
 * Class TagController
 * @package App\Http\Controllers\Api
 * @group Tags
 */
class TagController extends ApiController
{
    use FetchOne;
    public function __construct()
    {
        $this->authorizeResource(Tag::class);
    }

    /**
     * @param TagSchema $schema
     * @param AnonymousCollectionQuery $request
     * @return DataResponse
     */
     public function index(TagSchema $schema, AnonymousCollectionQuery $request): DataResponse
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

         $models = $models->whereIntegerInRaw('customer_id', $customerIds);

         if ($request->page()) {
             $models = $models->paginate($request->page());
         } else {
             $models = $models->get();
         }

         return new DataResponse($models);
     }
}
