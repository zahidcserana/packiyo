<?php

namespace App\JsonApi\V1\Webhooks;

use App\Models\Webhook;
use LaravelJsonApi\Eloquent\Contracts\Paginator;
use LaravelJsonApi\Eloquent\Fields\DateTime;
use LaravelJsonApi\Eloquent\Fields\ID;
use LaravelJsonApi\Eloquent\Fields\Str;
use LaravelJsonApi\Eloquent\Filters\Where;
use LaravelJsonApi\Eloquent\Filters\WhereIdIn;
use LaravelJsonApi\Eloquent\Pagination\PagePagination;
use LaravelJsonApi\Eloquent\Schema;

class WebhookSchema extends Schema
{

    /**
     * The model the schema corresponds to.
     *
     * @var string
     */
    public static string $model = Webhook::class;

    /**
     * Get the resource fields.
     *
     * @return array
     */
    public function fields(): array
    {
        return [
            ID::make(),
            Str::make('user_id')->sortable(),
            Str::make('customer_id')->sortable(),
            Str::make('order_channel_id')->sortable(),
            Str::make('name')->sortable(),
            Str::make('object_type')->sortable(),
            Str::make('operation')->sortable(),
            Str::make('url')->sortable(),
            Str::make('secret_key')->sortable(),
            DateTime::make('createdAt')->sortable()->readOnly(),
            DateTime::make('updatedAt')->sortable()->readOnly(),
        ];
    }

    /**
     * Get the resource filters.
     *
     * @return array
     */
    public function filters(): array
    {
        return [
            WhereIdIn::make($this),
            Where::make('user_id'),
            Where::make('customer_id'),
            Where::make('order_channel_id'),
            Where::make('name'),
            Where::make('object_type'),
            Where::make('operation'),
            Where::make('createdAt'),
            Where::make('updatedAt'),
            Where::make('deletedAt'),
            Where::make('createdAtFrom', 'created_at')->gte(),
            Where::make('createdAtTo', 'created_at')->lte(),
            Where::make('updatedAtFrom', 'updated_at')->gte(),
            Where::make('updatedAtTo', 'updated_at')->lte(),
            Where::make('deletedAtFrom', 'deleted_at')->gte(),
            Where::make('deletedAtTo', 'deleted_at')->lte(),
        ];
    }

    /**
     * Get the resource paginator.
     *
     * @return Paginator|null
     */
    public function pagination(): ?Paginator
    {
        return PagePagination::make();
    }

}
