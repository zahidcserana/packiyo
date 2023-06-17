<?php

namespace App\JsonApi\V1;

use App\JsonApi\V1\AccessTokens\AccessTokenSchema;
use App\JsonApi\V1\BillingContactInformations\BillingContactInformationSchema;
use App\JsonApi\V1\ContactInformations\ContactInformationSchema;
use App\JsonApi\V1\Customers\CustomerSchema;
use App\JsonApi\V1\InventoryLogs\InventoryLogSchema;
use App\JsonApi\V1\Locations\LocationSchema;
use App\JsonApi\V1\OrderItems\OrderItemSchema;
use App\JsonApi\V1\OrderLockInformations\OrderLockInformationSchema;
use App\JsonApi\V1\Orders\OrderSchema;
use App\JsonApi\V1\OrderChannels\OrderChannelSchema;
use App\JsonApi\V1\OrderStatuses\OrderStatusSchema;
use App\JsonApi\V1\CycleCountBatches\CycleCountBatchSchema;
use App\JsonApi\V1\PickingBatches\PickingBatchSchema;
use App\JsonApi\V1\PickingCarts\PickingCartsSchema;
use App\JsonApi\V1\PlacedToteOrderItems\PlacedToteOrderItemSchema;
use App\JsonApi\V1\Products\ProductSchema;
use App\JsonApi\V1\PurchaseOrderItems\PurchaseOrderItemSchema;
use App\JsonApi\V1\PurchaseOrders\PurchaseOrderSchema;
use App\JsonApi\V1\PurchaseOrderStatuses\PurchaseOrderStatusSchema;
use App\JsonApi\V1\ReturnItems\ReturnItemSchema;
use App\JsonApi\V1\Returns\ReturnSchema;
use App\JsonApi\V1\ReturnStatuses\ReturnStatusSchema;
use App\JsonApi\V1\Revisions\RevisionSchema;
use App\JsonApi\V1\Roles\RoleSchema;
use App\JsonApi\V1\Shipments\ShipmentSchema;
use App\JsonApi\V1\ShippingBoxes\ShippingBoxSchema;
use App\JsonApi\V1\Printers\PrinterSchema;
use App\JsonApi\V1\ShippingContactInformations\ShippingContactInformationSchema;
use App\JsonApi\V1\Suppliers\SupplierSchema;
use App\JsonApi\V1\Tags\TagSchema;
use App\JsonApi\V1\Tasks\TaskSchema;
use App\JsonApi\V1\TaskTypes\TaskTypeSchema;
use App\JsonApi\V1\ToteOrderItems\ToteOrderItemSchema;
use App\JsonApi\V1\Totes\ToteSchema;
use App\JsonApi\V1\Users\UserSchema;
use App\JsonApi\V1\Warehouses\WarehouseSchema;
use App\JsonApi\V1\Webhooks\WebhookSchema;
use App\JsonApi\V1\WebshipperCredentials\WebshipperCredentialSchema;
use App\JsonApi\V1\EasypostCredentials\EasypostCredentialSchema;
use LaravelJsonApi\Core\Server\Server as BaseServer;

class Server extends BaseServer
{

    /**
     * The base URI namespace for this server.
     *
     * @var string
     */
    protected string $baseUri = '/api/v1';

    /**
     * Bootstrap the server when it is handling an HTTP request.
     *
     * @return void
     */
    public function serving(): void
    {
        // no-op
    }

    /**
     * Get the server's list of schemas.
     *
     * @return array
     */
    protected function allSchemas(): array
    {
        return [
            UserSchema::class,
            ContactInformationSchema::class,
            RoleSchema::class,
            PickingBatchSchema::class,
            TaskSchema::class,
            TagSchema::class,
            OrderSchema::class,
            CustomerSchema::class,
            OrderItemSchema::class,
            ShipmentSchema::class,
            BillingContactInformationSchema::class,
            ShippingContactInformationSchema::class,
            OrderLockInformationSchema::class,
            OrderStatusSchema::class,
            OrderChannelSchema::class,
            ShippingBoxSchema::class,
            PrinterSchema::class,
            PurchaseOrderStatusSchema::class,
            TaskTypeSchema::class,
            SupplierSchema::class,
            PickingCartsSchema::class,
            PurchaseOrderSchema::class,
            ProductSchema::class,
            ReturnSchema::class,
            ReturnStatusSchema::class,
            WarehouseSchema::class,
            LocationSchema::class,
            WebhookSchema::class,
            WebshipperCredentialSchema::class,
            EasypostCredentialSchema::class,
            AccessTokenSchema::class,
            RevisionSchema::class,
            PurchaseOrderItemSchema::class,
            ReturnItemSchema::class,
            InventoryLogSchema::class,
            ToteOrderItemSchema::class,
            PlacedToteOrderItemSchema::class,
            ToteSchema::class,
            CycleCountBatchSchema::class,
        ];
    }
}
