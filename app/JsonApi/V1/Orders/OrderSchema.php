<?php

namespace App\JsonApi\V1\Orders;

use App\JsonApi\Filters\WhereLike;
use App\Models\Order;
use App\Models\OrderLock;
use LaravelJsonApi\Eloquent\Contracts\Paginator;
use LaravelJsonApi\Eloquent\Fields\DateTime;
use LaravelJsonApi\Eloquent\Fields\ID;
use LaravelJsonApi\Eloquent\Fields\Relations\BelongsTo;
use LaravelJsonApi\Eloquent\Fields\Relations\HasMany;
use LaravelJsonApi\Eloquent\Fields\Relations\HasOne;
use LaravelJsonApi\Eloquent\Filters\Scope;
use LaravelJsonApi\Eloquent\Filters\Where;
use LaravelJsonApi\Eloquent\Filters\WhereIdIn;
use LaravelJsonApi\Eloquent\Pagination\PagePagination;
use LaravelJsonApi\Eloquent\Schema;
use LaravelJsonApi\Eloquent\Fields\Str;

class OrderSchema extends Schema
{

    /**
     * The model the schema corresponds to.
     *
     * @var string
     */
    public static string $model = Order::class;

    /**
     * Get the resource fields.
     *
     * @return array
     */
    public function fields(): array
    {
        return [
            ID::make(),
            Str::make('order_status_id')->sortable(),
            Str::make('number')->sortable(),
            Str::make('external_id')->sortable(),
            Str::make('ordered_at')->sortable(),
            Str::make('required_shipping_date_at')->sortable(),
            Str::make('shipping_date_before_at')->sortable(),
            Str::make('slip_note')->sortable(),
            Str::make('priority')->sortable(),
            Str::make('created_at')->sortable(),
            Str::make('updated_at')->sortable(),
            Str::make('deleted_at')->sortable(),
            Str::make('priority_score')->sortable(),
            Str::make('gift_note')->sortable(),
            Str::make('tags')->sortable(),
            Str::make('fraud_hold')->sortable(),
            Str::make('allocation_hold')->sortable(),
            Str::make('address_hold')->sortable(),
            Str::make('payment_hold')->sortable(),
            Str::make('operator_hold')->sortable(),
            Str::make('allow_partial')->sortable(),
            Str::make('ready_to_ship')->sortable(),
            Str::make('ready_to_pick')->sortable(),
            Str::make('custom_invoice_url')->sortable(),
            HasOne::make('customer'),
            HasMany::make('order_items'),
            HasMany::make('shipments'),
            BelongsTo::make('shipping_contact_information'),
            BelongsTo::make('billing_contact_information'),
            HasOne::make('order_lock_information'),
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
            Where::make('order_status_id'),
            Where::make('number'),
            Where::make('external_id'),
            Where::make('ordered_at'),
            Where::make('required_shipping_date_at'),
            Where::make('shipping_date_before_at'),
            Where::make('slip_note'),
            Where::make('priority'),
            Where::make('created_at'),
            Where::make('updated_at'),
            Where::make('deleted_at'),
            Where::make('priority_score'),
            Where::make('gift_note'),
            Where::make('tags'),
            Where::make('fraud_hold'),
            Where::make('allocation_hold'),
            Where::make('address_hold'),
            Where::make('payment_hold'),
            Where::make('operator_hold'),
            Where::make('allow_partial'),
            Where::make('ready_to_ship'),
            Where::make('ready_to_pick'),
            Where::make('custom_invoice_url'),
            Where::make('customer'),
            Where::make('createdAt'),
            Where::make('updatedAt'),
            Where::make('deletedAt'),
            Where::make('createdAtFrom', 'created_at')->gte(),
            Where::make('createdAtTo', 'created_at')->lte(),
            Where::make('updatedAtFrom', 'updated_at')->gte(),
            Where::make('updatedAtTo', 'updated_at')->lte(),
            Where::make('deletedAtFrom', 'deleted_at')->gte(),
            Where::make('deletedAtTo', 'deleted_at')->lte(),
            Scope::make('not_picked_orders')->asBoolean(),
            WhereLike::make('partialNumber', 'number'),
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
