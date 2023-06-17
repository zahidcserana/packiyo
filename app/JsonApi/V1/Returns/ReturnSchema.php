<?php

namespace App\JsonApi\V1\Returns;

use App\Models\Return_;
use LaravelJsonApi\Eloquent\Contracts\Paginator;
use LaravelJsonApi\Eloquent\Fields\DateTime;
use LaravelJsonApi\Eloquent\Fields\ID;
use LaravelJsonApi\Eloquent\Fields\Relations\BelongsTo;
use LaravelJsonApi\Eloquent\Fields\Relations\HasMany;
use LaravelJsonApi\Eloquent\Fields\Str;
use LaravelJsonApi\Eloquent\Filters\Where;
use LaravelJsonApi\Eloquent\Filters\WhereIdIn;
use LaravelJsonApi\Eloquent\Pagination\PagePagination;
use LaravelJsonApi\Eloquent\Schema;

class ReturnSchema extends Schema
{

    /**
     * The model the schema corresponds to.
     *
     * @var string
     */
    public static string $model = Return_::class;

    /**
     * Get the resource fields.
     *
     * @return array
     */
    public function fields(): array
    {
        return [
            ID::make(),
            Str::make('number')->sortable(),
            DateTime::make('requestedAt')->sortable(),
            DateTime::make('expectedAt')->sortable(),
            DateTime::make('receivedAt')->sortable(),
            Str::make('reason')->sortable(),
            Str::make('approved')->sortable(),
            Str::make('notes'),
            BelongsTo::make('order'),
            HasMany::make('return_items'),
            DateTime::make('createdAt')->sortable()->readOnly(),
            DateTime::make('updatedAt')->sortable()->readOnly(),
            DateTime::make('deletedAt')->sortable()->readOnly(),
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
            Where::make('number'),
            Where::make('reason'),
            Where::make('approved'),
            Where::make('createdAt'),
            Where::make('updatedAt'),
            Where::make('deletedAt'),
            Where::make('requestedAt'),
            Where::make('expectedAt'),
            Where::make('receivedAt'),
            Where::make('createdAtFrom', 'created_at')->gte(),
            Where::make('createdAtTo', 'created_at')->lte(),
            Where::make('updatedAtFrom', 'updated_at')->gte(),
            Where::make('updatedAtTo', 'updated_at')->lte(),
            Where::make('deletedAtFrom', 'deleted_at')->gte(),
            Where::make('deletedAtTo', 'deleted_at')->lte(),
            Where::make('requestedAtFrom', 'requested_at')->gte(),
            Where::make('requestedAtTo', 'requested_at')->lte(),
            Where::make('expectedAtFrom', 'expected_at')->gte(),
            Where::make('expectedAtTo', 'expected_at')->lte(),
            Where::make('receivedAtFrom', 'received_at')->gte(),
            Where::make('receivedAtTo', 'received_at')->lte(),
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
