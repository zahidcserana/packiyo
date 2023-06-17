<?php

namespace App\JsonApi\V1\ContactInformations;

use App\Models\ContactInformation;
use LaravelJsonApi\Eloquent\Contracts\Paginator;
use LaravelJsonApi\Eloquent\Fields\DateTime;
use LaravelJsonApi\Eloquent\Fields\ID;
use LaravelJsonApi\Eloquent\Fields\Str;
use LaravelJsonApi\Eloquent\Filters\Where;
use LaravelJsonApi\Eloquent\Filters\WhereIdIn;
use LaravelJsonApi\Eloquent\Pagination\PagePagination;
use LaravelJsonApi\Eloquent\Schema;

class ContactInformationSchema extends Schema
{

    /**
     * The model the schema corresponds to.
     *
     * @var string
     */
    public static string $model = ContactInformation::class;

    /**
     * Get the resource fields.
     *
     * @return array
     */
    public function fields(): array
    {
        return [
            ID::make(),
            Str::make('name')->sortable(),
            Str::make('company_name')->sortable(),
            Str::make('object_type')->sortable(),
            Str::make('object_id')->sortable(),
            Str::make('company_name')->sortable(),
            Str::make('company_num')->sortable(),
            Str::make('address')->sortable(),
            Str::make('address2')->sortable(),
            Str::make('zip')->sortable(),
            Str::make('city')->sortable(),
            Str::make('email')->sortable(),
            Str::make('phone')->sortable(),
            DateTime::make('createdAt')->readOnly(),
            DateTime::make('updatedAt')->readOnly(),
            DateTime::make('deletedAt')->readOnly(),
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
            Where::make('name'),
            Where::make('company_name'),
            Where::make('object_type'),
            Where::make('object_id'),
            Where::make('company_name'),
            Where::make('company_num'),
            Where::make('address'),
            Where::make('address2'),
            Where::make('zip'),
            Where::make('city'),
            Where::make('email'),
            Where::make('phone'),
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
