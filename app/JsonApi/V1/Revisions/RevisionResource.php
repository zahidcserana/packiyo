<?php

namespace App\JsonApi\V1\Revisions;

use Illuminate\Http\Request;
use LaravelJsonApi\Core\Resources\JsonApiResource;

class RevisionResource extends JsonApiResource
{

    /**
     * Get the resource's attributes.
     *
     * @param Request|null $request
     * @return iterable
     */
    public function attributes($request): iterable
    {
        return [
            'id' => $this->id,
            'revisionable_type' => $this->revisionable_type,
            'revisionable_id' => $this->revisionable_id,
            'user_id' => $this->user_id,
            'key' => $this->key,
            'old_value' => $this->old_value,
            'new_value' => $this->new_value,
            'createdAt' => user_date_time($this->created_at),
            'updatedAt' => user_date_time($this->updated_at),
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
            // @TODO
        ];
    }

}
