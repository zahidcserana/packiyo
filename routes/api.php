<?php

use LaravelJsonApi\Laravel\Facades\JsonApiRoute;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/
Route::group(['middleware' => ['api-log', 'auth:api'], 'as' => 'api.'], function() {
    Route::post('dashboard/statistics', 'Api\HomeController@statistics')->name('dashboard.statistics');
    Route::get('profile', 'Api\ProfileController@edit')->name('profile.edit');
    Route::post('profile/password', 'Api\ProfileController@password')->name('profile.password');
    Route::post('profile/update', 'Api\ProfileController@update')->name('profile.update');
    Route::post('profile/upload', 'Api\ProfileController@upload')->name('profile.upload');
    Route::get('profile/delete', 'Api\ProfileController@delete')->name('profile.delete');
    Route::get('profile/logout', 'Api\ProfileController@logout')->name('profile.logout');
    JsonApiRoute::server('v1')
        ->namespace('Api')
        ->resources(function ($server) {
            $server->resource('users', UserController::class)
                ->actions(function ($actions) {
                    $actions->withId()->get('customers');
                    $actions->withId()->get('webhooks');
                    $actions->withId()->post('update');
            });
            $server->resource('tags', TagController::class);
            $server->resource('tasks', TaskController::class);
            $server->resource('tasks', BatchPickingController::class)->only()
                ->actions( function ($actions){
                    $actions->post('/picking-batches', 'pickingBatches');
                    $actions->post('/single-item-batch-picking', 'singleItemBatchPicking');
                    $actions->post('/single-order-picking', 'singleOrderPicking');
                    $actions->post('/multi-order-picking', 'multiOrderPicking');
                    $actions->post('/close-picking-task', 'closePickingTask');
                }
            );
            $server->resource('picking-batches', BatchPickingController::class)->only()
                ->actions( function ($actions){
                    $actions->post('/existing-items', 'existingItems');
                    $actions->post('/pick', 'pick');
                });
            $server->resource('tasks', CycleCountBatchController::class)->only()
                ->actions( function ($actions){
                    $actions->post('/close-counting-task', 'closeCountingTask');
                }
                );
            $server->resource('cycle-count-batches', CycleCountBatchController::class)->only()
                ->actions( function ($actions){
                    $actions->post('/available-batch', 'availableCountingBatch');
                    $actions->post('/close', 'closeCountingTask');
                    $actions->post('/count', 'count');
                    $actions->post('/pick', 'pick');
                });
            $server->resource('order-channels', OrderChannelController::class);
            $server->resource('orders', OrderController::class)
                ->actions(function ($actions){
                    $actions->get('filter');
                    $actions->withId()->post('ship');
                    $actions->withId()->post('markAsFulfilled');
                    $actions->withId()->post('cancel');
                    $actions->withId()->get('history');
                    $actions->get('items/{order_item}/history', 'itemHistory');
                    $actions->withId()->post('pick_to_tote', 'pickOrderItems');
                });
            $server->resource('order-statuses', OrderStatusController::class);
            $server->resource('shipping-boxes', ShippingBoxController::class);
            $server->resource('purchase-orders', PurchaseOrderController::class)
                ->actions(function ($actions){
                    $actions->get('filter');
                    $actions->withId()->post('receive');
                    $actions->withId()->post('close');
                    $actions->post('reject/{purchase_order_item}', 'reject');
                    $actions->withId()->get('history');
                    $actions->get('items/{purchase_order_item}/history', 'itemHistory');
            });
            $server->resource('purchase-order-statuses', PurchaseOrderStatusController::class);
            $server->resource('task-types', TaskTypeController::class);
            $server->resource('suppliers', SupplierController::class);
            $server->resource('products', ProductController::class)
                ->actions(function ($actions){
                    $actions->get('filter');
                    $actions->withId()->get('history');
                    $actions->withId()->post('transfer');
                    $actions->withId()->post('update');
                    $actions->withId()->post('change_location_quantity', 'changeLocationQuantity');
            });
            $server->resource('returns', ReturnController::class)
                ->actions(function ($actions){
                    $actions->get('filter');
                    $actions->withId()->post('receive');
                    $actions->withId()->get('history');
                    $actions->get('items/{return_item}/history', 'itemHistory');
            });
            $server->resource('return-statuses', ReturnStatusController::class);
            $server->resource('warehouses', WarehouseController::class);
            $server->resource('locations', LocationController::class);
            $server->resource('webhooks', WebhookController::class);
            $server->resource('customers', CustomerController::class)
                ->actions(function ($actions){
                    $actions->withId()->get('warehouses');
                    $actions->withId()->get('users');
                    $actions->withId()->get('tasks');
                    $actions->withId()->get('products');
                    $actions->withId()->get('user', 'listUsers');
                    $actions->withId()->put('user', 'updateUsers');
                    $actions->withId()->delete('user/{user}', 'detachUser');
                });
            $server->resource('webshipper-credentials', WebshipperCredentialController::class);
            $server->resource('easypost-credentials', EasypostCredentialController::class);
            $server->resource('inventory-logs', InventoryLogController::class);
            $server->resource('picking-carts', PickingCartController::class);
            $server->resource('totes', ToteController::class)
                ->actions(function ($actions) {
                    $actions->withId()->get('order_lines', 'toteOrderItems');
                    $actions->withId()->post('empty', 'emptyTote');
                    $actions->withId()->post('pick', 'pickOrderItems');
                });
            $server->resource('printers', PrinterController::class)
                ->actions(function ($actions) {
                    $actions->post('import');
                    $actions->get('userPrintersAndJobs', 'userPrintersAndJobs');
                    $actions->post('jobs/{printJob}/start', 'jobStart');
                    $actions->post('jobs/{printJob}/status', 'jobStatus');
                });
        });

//	Route::get('user/{user}/customers', 'Api\UserController@customers')->name('user.customers');
//	Route::get('user/{user}/webhooks', 'Api\UserController@webhooks')->name('user.webhooks');
//	Route::apiResource('user', 'Api\UserController');

//	Route::apiResource('purchase_order_status', 'Api\PurchaseOrderStatusController');
//	Route::apiResource('order_status', 'Api\OrderStatusController');
//	Route::apiResource('task_type', 'Api\TaskTypeController');

//	Route::apiResource('task', 'Api\TaskController');

//	Route::apiResource('supplier', 'Api\SupplierController');

//	Route::get('order/filter', 'Api\OrderController@filter')->name('order.filter');

//	Route::post('tasks/picking_batches', 'Api\BatchPickingController@pickingBatches')->name('picking_batches');
//	Route::post('picking_batches/{picking_batch}/pick', 'Api\BatchPickingController@pick');

//	Route::apiResource('order', 'Api\OrderController');

//	Route::post('order/{order}/ship', 'Api\OrderController@ship')->name('order.ship');

//    Route::get('order/{order}/history', 'Api\OrderController@history')->name('order.history');
//    Route::get('order_item/{order_item}/history', 'Api\OrderController@itemHistory')->name('order.itemHistory');

//	Route::get('purchase_order/filter', 'Api\PurchaseOrderController@filter')->name('purchase_order.filter');
//	Route::apiResource('purchase_order', 'Api\PurchaseOrderController');
//	Route::post('purchase_order/{purchase_order}/receive', 'Api\PurchaseOrderController@receive')->name('purchase_order.receive');

//    Route::get('purchase_order/{purchase_order}/history', 'Api\PurchaseOrderController@history')->name('purchase_order.history');
//    Route::get('purchase_order_item/{purchase_order_item}/history', 'Api\PurchaseOrderController@itemHistory')->name('purchase_order.itemHistory');

//	Route::get('return/filter', 'Api\ReturnController@filter')->name('return.filter');
//	Route::apiResource('return', 'Api\ReturnController');
//	Route::post('return/{return}/receive', 'Api\ReturnController@receive')->name('return.receive');

//    Route::get('return/{return}/history', 'Api\ReturnController@history')->name('return.history');
//    Route::get('return_item/{return_item}/history', 'Api\ReturnController@itemHistory')->name('return.itemHistory');

//	Route::apiResource('webhook', 'Api\WebhookController');

//	Route::apiResource('inventory_log', 'Api\InventoryLogController');

//	Route::get('product/filter', 'Api\ProductController@filter')->name('product.filter');

//	Route::apiResource('product', 'Api\ProductController');

//    Route::get('product/{product}/history', 'Api\ProductController@history')->name('product.history');

//	Route::apiResource('warehouse', 'Api\WarehouseController');

//	Route::apiResource('location', 'Api\LocationController');

//	Route::get('customer/{customer}/warehouses', 'Api\CustomerController@warehouses')->name('customer.warehouses');
//	Route::get('customer/{customer}/users', 'Api\CustomerController@users')->name('customer.users');
//	Route::get('customer/{customer}/tasks', 'Api\CustomerController@tasks')->name('customer.tasks');
//	Route::get('customer/{customer}/products', 'Api\CustomerController@products')->name('customer.products');
//	Route::get('customer/{customer}/user', 'Api\CustomerController@listUsers')->name('customer.list_users');
//	Route::put('customer/{customer}/user', 'Api\CustomerController@updateUsers')->name('customer.update_users');
//	Route::delete('customer/{customer}/user/{user}', 'Api\CustomerController@detachUser')->name('customer.detach_user');
//	Route::apiResource('customer', 'Api\CustomerController');

//	Route::apiResource('webshipper_credential', 'Api\WebshipperCredentialController');
});

Route::post('login', 'Api\LoginController@authenticate')->name('login.authenticate');
