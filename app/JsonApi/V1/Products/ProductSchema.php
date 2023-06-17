<?php

namespace App\JsonApi\V1\Products;

use App\JsonApi\Filters\WhereLike;
use App\Models\Product;
use LaravelJsonApi\Eloquent\Contracts\Paginator;
use LaravelJsonApi\Eloquent\Fields\DateTime;
use LaravelJsonApi\Eloquent\Fields\ID;
use LaravelJsonApi\Eloquent\Fields\Relations\HasOne;
use LaravelJsonApi\Eloquent\Fields\Str;
use LaravelJsonApi\Eloquent\Filters\Where;
use LaravelJsonApi\Eloquent\Filters\WhereIdIn;
use LaravelJsonApi\Eloquent\Pagination\PagePagination;
use LaravelJsonApi\Eloquent\Schema;

class ProductSchema extends Schema
{

    /**
     * The model the schema corresponds to.
     *
     * @var string
     */
    public static string $model = Product::class;

    /**
     * Get the resource fields.
     *
     * @return array
     */
    public function fields(): array
    {

        return [
            ID::make(),
            Str::make('sku')->sortable(),
            Str::make('name')->sortable(),
            Str::make('price')->sortable(),
            Str::make('notes')->sortable(),
            Str::make('quantity_on_hand')->sortable(),
            Str::make('quantity_allocated')->sortable(),
            Str::make('quantity_available')->sortable(),
            Str::make('quantity_backordered')->sortable(),
            DateTime::make('createdAt')->sortable()->readOnly(),
            DateTime::make('updatedAt')->sortable()->readOnly(),
            DateTime::make('deletedAt')->sortable()->readOnly(),
            HasOne::make('customer'),
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
            Where::make('sku'),
            Where::make('name'),
            Where::make('barcode'),
            Where::make('customer_id'),
            Where::make('price'),
            Where::make('notes'),
            Where::make('quantity_on_hand'),
            Where::make('quantity_allocated'),
            Where::make('quantity_available'),
            Where::make('quantity_backordered'),
            Where::make('createdAtFrom', 'created_at')->gte(),
            Where::make('createdAtTo', 'created_at')->lte(),
            Where::make('updatedAtFrom', 'updated_at')->gte(),
            Where::make('updatedAtTo', 'updated_at')->lte(),
            Where::make('deletedAtFrom', 'deleted_at')->gte(),
            Where::make('deletedAtTo', 'deleted_at')->lte(),
            Where::make('createdAt'),
            Where::make('updatedAt'),
            Where::make('deletedAt'),
            WhereLike::make('partialSkuOrNameOrBarcode', 'sku|name|barcode'),
            WhereLike::make('partialSkuOrName', 'sku|name'),
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
