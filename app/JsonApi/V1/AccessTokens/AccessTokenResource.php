<?php

namespace App\JsonApi\V1\AccessTokens;

use Illuminate\Http\Request;
use LaravelJsonApi\Core\Resources\JsonApiResource;

class AccessTokenResource extends JsonApiResource
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
            'user_id' => $this->userId,
            'client_id' => $this->client_id,
            'name' => $this->name,
            'scopes' => $this->scopes,
            'revoked' => $this->revoked,
            'createdAt' => $this->created_at,
            'updatedAt' => $this->updated_at,
            'expiresAt' => $this->expires_at,
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
