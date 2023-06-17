<?php

namespace App\JsonApi\V1\EasypostCredentials;

use Illuminate\Http\Request;
use LaravelJsonApi\Core\Resources\JsonApiResource;

class EasypostCredentialResource extends JsonApiResource
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
            'api_key' => $this->api_key,
            'api_base_url' => $this->api_base_url,
            'createdAt' => $this->created_at,
            'updatedAt' => $this->updated_at,
            'deletedAt' => $this->deletedAt,
            'customer' => $this->customer->load('contactInformation'),
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
            $this->relation('customer')->showDataIfLoaded()->withoutLinks()
        ];
    }

}
