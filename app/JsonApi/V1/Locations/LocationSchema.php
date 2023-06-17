<?php

namespace App\JsonApi\V1\Locations;

use App\JsonApi\Filters\WhereLike;
use App\Models\Location;
use LaravelJsonApi\Eloquent\Contracts\Paginator;
use LaravelJsonApi\Eloquent\Fields\DateTime;
use LaravelJsonApi\Eloquent\Fields\ID;
use LaravelJsonApi\Eloquent\Fields\Relations\BelongsTo;
use LaravelJsonApi\Eloquent\Fields\Str;
use LaravelJsonApi\Eloquent\Filters\Where;
use LaravelJsonApi\Eloquent\Filters\WhereIdIn;
use LaravelJsonApi\Eloquent\Filters\WhereIdNotIn;
use LaravelJsonApi\Eloquent\Pagination\PagePagination;
use LaravelJsonApi\Eloquent\Schema;

class LocationSchema extends Schema
{

    /**
     * The model the schema corresponds to.
     *
     * @var string
     */
    public static string $model = Location::class;

    /**
     * Get the resource fields.
     *
     * @return array
     */
    public function fields(): array
    {
        return [
            ID::make(),
            Str::make('name'),
            Str::make('pickable'),
            BelongsTo::make('warehouse'),
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
            WhereIdNotIn::make($this, 'exclude')->delimiter(','),
            Where::make('name'),
            Where::make('barcode'),
            Where::make('pickable'),
            Where::make('createdAt'),
            Where::make('updatedAt'),
            Where::make('deletedAt'),
            Where::make('createdAtFrom', 'created_at')->gte(),
            Where::make('createdAtTo', 'created_at')->lte(),
            Where::make('updatedAtFrom', 'updated_at')->gte(),
            Where::make('updatedAtTo', 'updated_at')->lte(),
            Where::make('deletedAtFrom', 'deleted_at')->gte(),
            Where::make('deletedAtTo', 'deleted_at')->lte(),
            WhereLike::make('partialNameOrBarcode', 'name|barcode'),
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
