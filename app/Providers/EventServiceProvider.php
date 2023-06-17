<?php

namespace App\Providers;

use App\Listeners\WebhookCallEventSubscriber;
use App\Models\Customer;
use App\Models\EasypostCredential;
use App\Models\Location;
use App\Models\LocationType;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\PickingBatchItem;
use App\Models\Product;
use App\Models\PurchaseOrderItem;
use App\Models\ShippingMethodMapping;
use App\Models\ToteOrderItem;
use App\Models\User;
use App\Models\Warehouse;
use App\Models\WebshipperCredential;
use App\Observers\CustomerObserver;
use App\Observers\EasypostCredentialObserver;
use App\Observers\LocationObserver;
use App\Observers\LocationTypeObserver;
use App\Observers\OrderItemObserver;
use App\Observers\OrderObserver;
use App\Observers\PickingBatchItemObserver;
use App\Observers\ProductObserver;
use App\Observers\PurchaseOrderItemObserver;
use App\Observers\ShippingMethodMappingObserver;
use App\Observers\ToteOrderItemObserver;
use App\Observers\UserObserver;
use App\Observers\WarehouseObserver;
use OwenIt\Auditing\Events\Auditing;
use App\Listeners\AuditingListener;
use App\Observers\WebshipperCredentialObserver;
use Illuminate\Auth\Events\Registered;
use Illuminate\Auth\Listeners\SendEmailVerificationNotification;
use Illuminate\Foundation\Support\Providers\EventServiceProvider as ServiceProvider;

class EventServiceProvider extends ServiceProvider
{
    /**
     * The event listener mappings for the application.
     *
     * @var array
     */
    protected $listen = [
        Registered::class => [
            SendEmailVerificationNotification::class,
        ],
        Auditing::class => [
           AuditingListener::class
        ]
    ];

    /**
     * The subscriber classes to register.
     *
     * @var array
     */
    protected $subscribe = [
        WebhookCallEventSubscriber::class
    ];

    /**
     * Register any events for your application.
     *
     * @return void
     */
    public function boot()
    {
        User::observe(UserObserver::class);
        Product::observe(ProductObserver::class);
        Order::observe(OrderObserver::class);
        OrderItem::observe(OrderItemObserver::class);
        PurchaseOrderItem::observe(PurchaseOrderItemObserver::class);
        Customer::observe(CustomerObserver::class);
        Warehouse::observe(WarehouseObserver::class);
        Location::observe(LocationObserver::class);
        LocationType::observe(LocationTypeObserver::class);
        ShippingMethodMapping::observe(ShippingMethodMappingObserver::class);
        WebshipperCredential::observe(WebshipperCredentialObserver::class);
        EasypostCredential::observe(EasypostCredentialObserver::class);
        ToteOrderItem::observe(ToteOrderItemObserver::class);
        PickingBatchItem::observe(PickingBatchItemObserver::class);
    }
}
