<?php

namespace App\Providers;

use App\Models\Customer;
use App\Models\InventoryLog;
use App\Models\Location;
use App\Models\LocationType;
use App\Models\Order;
use App\Models\OrderChannel;
use App\Models\OrderStatus;
use App\Models\PickingCart;
use App\Models\Printer;
use App\Models\PrintJob;
use App\Models\Product;
use App\Models\PurchaseOrder;
use App\Models\PurchaseOrderStatus;
use App\Models\Return_;
use App\Models\ShippingMethod;
use App\Models\Supplier;
use App\Models\Task;
use App\Models\TaskType;
use App\Models\Tote;
use App\Models\User;
use App\Models\UserRole;
use App\Models\Warehouse;
use App\Models\Webhook;
use App\Models\WebshipperCredential;
use App\Models\EasypostCredential;
use App\Policies\CustomerPolicy;
use App\Policies\InventoryLogPolicy;
use App\Policies\LocationPolicy;
use App\Policies\LocationTypesPolicy;
use App\Policies\OrderChannelPolicy;
use App\Policies\OrderPolicy;
use App\Policies\OrderStatusPolicy;
use App\Policies\PickingCartPolicy;
use App\Policies\PrinterPolicy;
use App\Policies\ProductPolicy;
use App\Policies\PurchaseOrderPolicy;
use App\Policies\PurchaseOrderStatusPolicy;
use App\Policies\ReturnsPolicy;
use App\Policies\ShippingMethodPolicy;
use App\Policies\SupplierPolicy;
use App\Policies\TaskPolicy;
use App\Policies\TaskTypePolicy;
use App\Policies\TotePolicy;
use App\Policies\LotPolicy;
use App\Policies\UserPolicy;
use App\Policies\UserRolePolicy;
use App\Policies\WarehousePolicy;
use App\Policies\WebhookPolicy;
use App\Policies\WebshipperCredentialPolicy;
use App\Policies\EasypostCredentialPolicy;
use Illuminate\Foundation\Support\Providers\AuthServiceProvider as ServiceProvider;

class AuthServiceProvider extends ServiceProvider
{
    /**
     * The policy mappings for the application.
     *
     * @var array
     */
    protected $policies = [
        User::class => UserPolicy::class,
        UserRole::class => UserRolePolicy::class,
        TaskType::class => TaskTypePolicy::class,
        Task::class => TaskPolicy::class,
        Supplier::class => SupplierPolicy::class,
        OrderChannel::class => OrderChannelPolicy::class,
        Order::class => OrderPolicy::class,
        Printer::class => PrinterPolicy::class,
        PrintJob::class => PrinterPolicy::class,
        PurchaseOrder::class => PurchaseOrderPolicy::class,
        Return_::class => ReturnsPolicy::class,
        Warehouse::class => WarehousePolicy::class,
        Location::class => LocationPolicy::class,
        Product::class => ProductPolicy::class,
        Webhook::class => WebhookPolicy::class,
        InventoryLog::class => InventoryLogPolicy::class,
        Customer::class => CustomerPolicy::class,
        OrderStatus::class => OrderStatusPolicy::class,
        PurchaseOrderStatus::class => PurchaseOrderStatusPolicy::class,
        WebshipperCredential::class => WebshipperCredentialPolicy::class,
        EasypostCredential::class => EasypostCredentialPolicy::class,
        Tote::class => TotePolicy::class,
        Lot::class => LotPolicy::class,
        PickingCart::class => PickingCartPolicy::class,
        LocationType::class => LocationTypesPolicy::class,
        ShippingMethod::class => ShippingMethodPolicy::class,
    ];

    /**
     * Register any authentication / authorization services.
     *
     * @return void
     */
    public function boot()
    {
        $this->registerPolicies();
    }
}
