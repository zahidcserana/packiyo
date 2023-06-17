<?php

namespace App\JsonApi\V1\Webhooks;

use Illuminate\Http\Request;
use LaravelJsonApi\Core\Resources\JsonApiResource;

class WebhookResource extends JsonApiResource
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
            'user_id' => $this->user_id,
            'customer_id' => $this->customer_id,
            'order_channel_id' => $this->order_channel_id,
            'name' => $this->name,
            'object_type' => $this->object_type,
            'operation' => $this->operation,
            'url' => $this->url,
            'secret_key' => $this->secret_key,
            'createdAt' => user_date_time($this->created_at),
            'updatedAt' => user_date_time($this->updated_at),
            'deletedAt' => user_date_time($this->deleted_at),
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
