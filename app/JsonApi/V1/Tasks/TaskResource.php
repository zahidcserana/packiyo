<?php

namespace App\JsonApi\V1\Tasks;

use App\Http\Resources\UserResource;
use Illuminate\Http\Request;
use LaravelJsonApi\Core\Resources\JsonApiResource;

class TaskResource extends JsonApiResource
{

    /**
     * Get the resource's attributes.
     *
     * @param Request|null $request
     * @return iterable
     */
    public function attributes($request): iterable
    {
        $this->user = new UserResource($this->user);

        return [
            'id' => $this->id,
            'taskable_type' => $this->created_at,
            'notes' => $this->created_at,
            'user' => $this->user,
            'created_at' => $this->created_at,
            'updated_at' => $this->created_at,
            'deleted_at' => $this->created_at,
        ];
    }

    /**
     * Get the resource's relationships.
     *
     * @param Request|null $request
     * @return iterable
     */
    public function relationships($request): iterable
    {
        return [
            $this->relation('contact_information')->showDataIfLoaded()->withoutLinks(),
        ];
    }

}
