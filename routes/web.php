<?php

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

use App\Providers\RouteServiceProvider;

Route::get('/', function () {
	if (Auth::check()) {
        return redirect(RouteServiceProvider::HOME);
    }

    return redirect('login');
})->name('welcome');

Auth::routes(['register' => false]);

Route::get('dashboard', 'HomeController@index')->name('home');
Route::get('pricing', 'PageController@pricing')->name('page.pricing');
Route::get('lock', 'PageController@lock')->name('page.lock');

Route::group(['middleware' => 'auth'], static function () {

    Route::post('search', 'SearchController@getSearch')->name('search.form');
    Route::get('search/{keyword}', 'SearchController@index')->name('search');

    // widgets stuff
    Route::get('dashboard/orders_revenue', 'HomeController@totalRevenue')->name('dashboard.orders_revenue');
    Route::get('dashboard/purchase_orders_received', 'HomeController@purchaseOrdersReceived')->name('dashboard.purchase_orders_received');
    Route::get('dashboard/late_orders', 'HomeController@lateOrders')->name('dashboard.late_orders');
    Route::get('orders/orders_by_cities', 'HomeController@ordersByCities');

    Route::get('site/filterCountries', 'SiteController@filterCountries')->name('site.filterCountries');

    Route::get('orders/orders_by_cities_limited', 'HomeController@ordersByCities');
    Route::get('orders/orders_by_country', 'HomeController@ordersByCountry');
    Route::get('orders/orders_received_count', 'HomeController@ordersReceivedCalc');
    Route::get('orders/orders_shipped_count', 'HomeController@shipmentsCalc');

    Route::get('returns/returns-count', 'HomeController@returnsCalc');
    Route::get('purchase_orders/coming_in', 'HomeController@purchaseOrdersCalc');
    Route::get('purchase_orders/quantity_calc', 'HomeController@purchaseOrdersQuantityCalc');

	Route::resource('role', 'RoleController', ['except' => ['show', 'destroy']]);
    Route::get('user/data-table', 'UserController@dataTable')->name('user.dataTable');
    Route::resource('user', 'UserController', ['except' => ['show']]);


    Route::get('profile', ['as' => 'profile.edit', 'uses' => 'ProfileController@edit']);
    Route::get('profile/activity', ['as' => 'profile.activity', 'uses' => 'ProfileController@activity']);
    Route::get('profile/activity/data-table', ['as' => 'profile.activity.datatable', 'uses' => 'ProfileController@dataTableActivity']);
	Route::put('profile', ['as' => 'profile.update', 'uses' => 'ProfileController@update']);
	Route::put('profile/password', ['as' => 'profile.password', 'uses' => 'ProfileController@password']);
	Route::post('profile/access_token', 'ProfileController@createAccessToken')->name('profile.create_access_token');
	Route::delete('profile/access_tokens/{token}', 'ProfileController@deleteAccessToken')->name('profile.delete_access_token');

    Route::get('customer/data-table', 'CustomerController@dataTable')->name('customer.dataTable');
    Route::delete('customer/{customer}/detachUser/{user}', 'CustomerController@detachUser')->name('customer.detachUser');

    Route::get('customer/{customer}/users', 'CustomerController@edit')->name('customer.editUsers');
    Route::post('customer/{customer}/users/update', 'CustomerController@updateUsers')->name('customer.updateUsers');

    Route::get('customers/{customer}/easypost_credentials/{easypost_credential}/batches', 'EasypostCredentialController@batches')->name('customers.easypost_credentials.batches');
    Route::get('customers/{customer}/easypost_credentials/{easypost_credential}/batch_shipments', 'EasypostCredentialController@batchShipments')->name('customers.easypost_credentials.batch_shipments');
    Route::get('customers/{customer}/easypost_credentials/{easypost_credential}/scanform_batches', 'EasypostCredentialController@scanformBatches')->name('customers.easypost_credentials.scanform_batches');
    Route::resource('customers.easypost_credentials', 'EasypostCredentialController');

    Route::resource('customers.webshipper_credentials', 'WebshipperCredentialController');

    Route::get('customer/{customer}/cssOverrides', 'CustomerController@edit')->name('customer.cssOverrides');

    Route::get('customer/{customer}/filterUsers', 'CustomerController@filterusers')->name('customer.filterUsers');
    Route::get('customer/{customer}/dimension_units', 'CustomerController@getDimensionUnits')->name('customer.dimensionUnits');
    Route::resource('customer', 'CustomerController');

    Route::get('product/data-table', 'ProductController@dataTable')->name('product.dataTable');
    Route::get('product/filterCustomers', 'ProductController@filterCustomers')->name('product.filterCustomers');
    Route::get('product/filterSuppliers/{customer?}', 'ProductController@filterSuppliers')->name('product.filterSuppliers');
    Route::get('product/filter/{customer?}', 'ProductController@filter')->name('product.filter');
    Route::get('product/filterBySupplier/{supplier?}', 'ProductController@filterBySupplier')->name('product.filterBySupplier');
    Route::get('product/delete_product_image', 'ProductController@deleteProductImage')->name('products.delete_product_image');
    Route::get('product/getProduct/{product}', 'ProductController@getItem')->name('product.getProduct');
    Route::get('product/deleteKitProduct/{product}', 'ProductController@deleteItem')->name('product.deloeteKitProduct');
    Route::post('product/removeKitProduct/{product}/{parentId?}', 'ProductController@removeItem')->name('product.removeKitProduct');
    Route::post('product/updateKitProduct/{product}', 'ProductController@updateItem')->name('product.updateKitProduct');
    Route::get('product/{product}/getLog', 'ProductController@getLog')->name('product.getLog');
    Route::get('product/filterLocations/{product}', 'ProductController@filterLocations')->name('product.filterLocations');
    Route::post('product/transfer/{product}', 'ProductController@transfer')->name('product.transfer');
    Route::post('product/changeLocationQuantity/{product}', 'ProductController@changeLocationQuantity')->name('product.changeLocationQuantity');
    Route::post('product/removeFromLocation/{product}', 'ProductController@removeFromLocation')->name('product.removeFromLocation');
    Route::post('product/addToLocation/{product}', 'ProductController@addToLocation')->name('product.addToLocation');
    Route::get('product/filterKitProducts/{customer?}', 'ProductController@filterKitProducts')->name('product.filterKitProducts');
    Route::get('product/locations', 'LocationController@productLocations')->name('productLocation.index');
    Route::get('product/locations/{product}', 'ProductController@locationsDataTable')->name('product.locationsDataTable');
    Route::get('product/{product}/locations', 'ProductController@getLocations')->name('productLocation.getLocations');
    Route::post('product/import/csv', 'ProductController@importCsv')->name('product.importCsv');
    Route::post('product/export/csv', 'ProductController@exportCsv')->name('product.exportCsv');
    Route::post('product/import/csv/{product}', 'ProductController@importKitItemsCsv')->name('product.importKitItemsCsv');
    Route::post('product/export/csv/{product}', 'ProductController@exportKitItemsCsv')->name('product.exportKitItemsCsv');
    Route::post('product/{product}/recover', 'ProductController@recover')->name('product.recover');
    Route::get('product/search/{keyword}', 'ProductController@index')->name('product.search');
    Route::get('product/order-items-data-table/{product}', 'ProductController@orderItemsDataTable')->name('product.orderItemsDataTable');
    Route::get('product/shipped-items-data-table/{product}', 'ProductController@shippedItemsDataTable')->name('product.shippedItemsDataTable');
    Route::get('product/tote-items-data-table/{product}', 'ProductController@toteItemsDataTable')->name('product.toteItemsDataTable');
    Route::get('product/kits-data-table/{product}', 'ProductController@kitsDataTable')->name('product.kitsDataTable');
    Route::post('product/bulkSelectionStatus', 'ProductController@getBulkSelectionStatus')->name('product.bulk_status');
    Route::post('product/bulk-edit', 'ProductController@bulkEdit')->name('product.bulk_edit');
    Route::post('product/bulk-delete', 'ProductController@bulkDelete')->name('product.bulk_delete');
    Route::post('product/bulk-recover', 'ProductController@bulkRecover')->name('product.bulk_recover');
    Route::resource('product', 'ProductController');
    Route::post('product/{product}/barcodes', 'ProductController@barcodes')->name('product.barcodes');
    Route::get('product/{product}/customer-printers', 'ProductController@getCustomerPrinters')->name('product.getCustomerPrinters');
    Route::get('product/{product}/barcode-pdf', 'ProductController@getBarcodePDF')->name('product.getBarcodePDF');
    Route::get('product/{product}/audits', 'ProductController@audits')->name('product.audits');

    Route::get('/orders/get_order_status/{customer?}', 'OrderController@getOrderStatus')->name('order.filterOrderStatuses');
    Route::post('order/countRecords', 'OrderController@countRecords')->name('order.countRecords');
    Route::post('order/bulkOrderStatus', 'OrderController@getBulkOrderStatus')->name('order.bulkOrderStatus');
    Route::get('order/{order}/delete', 'OrderController@destroy')->name('order.filterCustomers');
    Route::post('order/{order}/cancel', 'OrderController@cancelOrder')->name('order.cancel');
    Route::post('order/{order}/reship', 'OrderController@reship')->name('order.reship');
    Route::post('order/{order}/{orderItem}/cancel', 'OrderController@cancelOrderItem')->name('orderItem.cancel');
    Route::post('order/{order}/fulfill', 'OrderController@fulfillOrder')->name('order.fulfill');
    Route::get('order/getOrder/{order}', 'OrderController@getItem')->name('product.getOrder');
    Route::get('order/getKitItems/{orderItem}', 'OrderController@getKitItems')->name('product.getKitItems');
    Route::get('order/edit/{order}', 'OrderController@edit')->name('order.edit');
    Route::get('order/filterCustomers', 'OrderController@filterCustomers')->name('order.filterCustomers');
    Route::post('order/import/csv', 'OrderController@importCsv')->name('order.importCsv');
    Route::post('order/export/csv', 'OrderController@exportCsv')->name('order.exportCsv');
    Route::get('order/filterProducts/{customer?}', 'OrderController@filterProducts')->name('order.filterProducts');
    Route::get('order/getOrderReturnForm/{order}', 'OrderController@getOrderReturnForm');
    Route::get('order/data-table', 'OrderController@dataTable')->name('order.dataTable');
    Route::post('order/bulk-edit', 'OrderController@bulkEdit')->name('order.bulkEdit');
    Route::post('order/bulk-cancel', 'OrderController@bulkCancel')->name('order.bulk_cancel');
    Route::post('order/bulk-mark-as-fulfilled', 'OrderController@bulkFulfill')->name('order.bulk_mark_as_fulfilled');
    Route::put('order/{order}/return', 'OrderController@return')->name('order.return');

    Route::get('order/{order}/webshipper/shipping_rates', 'OrderController@webshipperShippingRates');
    Route::get('order/search/{keyword}', 'OrderController@index')->name('order.search');

    Route::post('shipments/{shipment}/void', 'ShipmentController@void')->name('shipments.void');

    Route::resource('order', 'OrderController');
    Route::get('order/{order}/audits', 'OrderController@audits')->name('order.audits');

    Route::get('order_status/filterCustomers', 'OrderStatusController@filterCustomers')->name('order_status.filterCustomers');
    Route::get('order_status/data-table', 'OrderStatusController@dataTable')->name('order_status.dataTable');
    Route::resource('order_status', 'OrderStatusController');

    Route::get('shipping_box/filterCustomers', 'ShippingBoxController@filterCustomers')->name('shipping_box.filterCustomers');
    Route::get('shipping_box/data-table', 'ShippingBoxController@dataTable')->name('shipping_box.dataTable');
    Route::resource('shipping_box', 'ShippingBoxController')->middleware('3pl');

    Route::prefix('packing')->middleware('3pl')->group(function() {
        Route::prefix('single_order_shipping')->group(function() {
            Route::get('/', 'PackingController@singleOrderShippingDataTable')->name('packing.single_order_shipping.dataTable');
            Route::get('{order}', 'PackingController@singleOrderShipping')->name('packing.single_order_shipping');
            Route::post('ship/{order}', 'PackingController@singleOrderShip')->name('packing.ship');
            Route::get('barcode_search/{barcode}', 'PackingController@barcodeSearch')->name('packing.barcodeSearch');
        });

        Route::prefix('bulk_shipping')->group(function() {
            Route::get('/', 'BulkShippingController@index')->name('bulk_shipping.index');
            Route::get('batches', 'BulkShippingController@batches')->name('bulk_shipping.batches');
            Route::get('{bulkShipBatch}', 'PackingController@bulkShipBatchShipping')->name('bulk_shipping.shipping');
            Route::post('{bulkShipBatch}', 'PackingController@bulkShipBatchShip')->name('bulk_shipping.ship');
            Route::post('markAsPrinted/{bulkShipBatch}', 'BulkShippingController@markAsPrinted')->name('bulk_shipping.markAsPrinted');
            Route::post('markAsPacked/{bulkShipBatch}', 'BulkShippingController@markAsPacked')->name('bulk_shipping.markAsPacked');
        });
    });
    Route::resource('packing', 'PackingController')->middleware('3pl');

    Route::get('purchase_order_status/filterCustomers', 'PurchaseOrderStatusController@filterCustomers')->name('purchase_order_status.filterCustomers');
    Route::resource('purchase_order_status', 'PurchaseOrderStatusController');

    Route::get('purchase_orders/data-table', 'PurchaseOrderController@dataTable')->name('purchase_order.dataTable');
    Route::get('/purchase_orders/get_order_status/{customer}', 'PurchaseOrderController@getOrderStatus');
    Route::get('purchase_orders/filterProducts', 'PurchaseOrderController@filterProducts')->name('purchase_order.filterProducts');
    Route::get('purchase_orders/filterCustomers', 'PurchaseOrderController@filterCustomers')->name('purchase_order.filterCustomers');
    Route::get('purchase_orders/filterLocations', 'PurchaseOrderController@filterLocations')->name('purchase_order.filterLocations');
    Route::get('purchase_orders/filterWarehouses/{customer?}', 'PurchaseOrderController@filterWarehouses')->name('purchase_order.filterWarehouses');
    Route::get('purchase_orders/filterSuppliers/{customer?}', 'PurchaseOrderController@filterSuppliers')->name('purchase_order.filterSuppliers');
    Route::get('purchase_orders/getPurchaseOrderModal/{purchaseOrder}', 'PurchaseOrderController@getPurchaseOrderModal')->name('purchase_order.getPurchaseOrderModal');
    Route::get('purchase_orders/receive/{purchaseOrder}', 'PurchaseOrderController@receivePurchaseOrder')->name('purchase_order.receive')->middleware('3pl');
    Route::post('purchase_orders/update/{purchaseOrder}', 'PurchaseOrderController@updatePurchaseOrder')->name('purchase_order.updatePurchaseOrder');
    Route::post('purchase_orders/close/{purchaseOrder}', 'PurchaseOrderController@close')->name('purchase_order.close');
    Route::get('purchase_orders/getRejectedPurchaseOrderItemModal/{purchaseOrderItem}', 'PurchaseOrderController@getRejectedPurchaseOrderItemModal')->name('purchase_order.getRejectedPurchaseOrderItemModal');
    Route::post('purchase_orders/reject/{purchaseOrderItem}', 'PurchaseOrderController@reject')->name('purchase_order.reject');
    Route::get('purchase_orders/search/{keyword}', 'PurchaseOrderController@index')->name('purchase_orders.search');
    Route::post('purchase_orders/import/csv', 'PurchaseOrderController@importCsv')->name('purchase_order.importCsv');
    Route::post('purchase_orders/export/csv', 'PurchaseOrderController@exportCsv')->name('purchase_order.exportCsv');
    Route::post('purchase_orders/bulk-edit', 'PurchaseOrderController@bulkEdit')->name('purchase_order.bulkEdit');
    Route::resource('purchase_orders', 'PurchaseOrderController');

    Route::get('return/data-table', 'ReturnController@dataTable')->name('return.dataTable');
    Route::get('return/filterOrderProducts/{orderId}', 'ReturnController@filterOrderProducts')->name('return.filterOrderProducts');
    Route::get('return/filterOrders', 'ReturnController@filterOrders')->name('return.filterOrders');
    Route::get('return/filterStatuses', 'ReturnController@filterStatuses')->name('return.filterStatuses');
    Route::get('return/filterLocations', 'ReturnController@filterLocations')->name('return.filterLocations');
    Route::get('return/create/{order?}', 'ReturnController@create')->name('return.create');
    Route::get('return/status/{return}', 'ReturnController@status')->name('return.status');
    Route::put('return/status/{return}', 'ReturnController@statusUpdate')->name('return.statusUpdate');
    Route::post('return/bulk-edit', 'ReturnController@bulkEdit')->name('return.bulkEdit');

    Route::get('return/search/{keyword}', 'ReturnController@index')->name('return.search');
    Route::resource('return', 'ReturnController', ['except' => ['create']]);

    Route::get('return_status/data-table', 'ReturnStatusController@dataTable')->name('return_status.dataTable');
    Route::resource('return_status', 'ReturnStatusController');

    Route::get('lot/data-table', 'LotController@dataTable')->name('lot.dataTable');
    Route::get('lot/filterLots', 'LotController@filterLots')->name('lot.filterLots');
    Route::resource('lot', 'LotController');

    Route::get('warehouses/data-table', 'WarehouseController@dataTable')->name('warehouse.dataTable');
    Route::get('warehouses/{warehouse}/edit/location', 'WarehouseController@edit')->name('warehouses.editWarehouseLocation');
    Route::post('warehouses/{warehouse}/addUsers', 'WarehouseController@addCustomers')->name('warehouse.addCustomers');
    Route::get('warehouses/filterCustomers', 'WarehouseController@filterCustomers')->name('warehouses.filterCustomers');
    Route::get('warehouses/getWarehouseModal/{warehouse?}', 'WarehouseController@getWarehouseModal')->name('warehouses.getWarehouseModal');
    Route::resource('warehouses', 'WarehouseController', ['except' => ['show']]);

    Route::get('warehouses/{warehouse}/edit/location/create', 'LocationController@create')->name('warehouseLocation.create');
    Route::post('warehouses/{warehouse}/edit/location/store', 'LocationController@storeWarehouseLocation')->name('warehouseLocation.store');
    Route::delete('warehouses/{warehouse}/edit/warehouse/location/{location}', 'LocationController@destroy')->name('warehouseLocation.destroy');
    Route::get('warehouses/{warehouse}/edit/location/{location}', 'LocationController@edit')->name('warehouseLocation.edit');
    Route::post('warehouses/{warehouse}/edit/location/{location}/update', 'LocationController@update')->name('warehouseLocation.update');

//    Session customer
    Route::get('user_customer/set/{customer}', 'UserController@setSessionCustomer')->name('user.setSessionCustomer');
    Route::get('user_customer/forget', 'UserController@removeSessionCustomer')->name('user.removeSessionCustomer');
    Route::get('user_customer/all', 'UserController@getCustomers')->name('user.getCustomers');

    Route::get('task_type/data-table', 'TaskTypeController@dataTable')->name('task_type.dataTable');
    Route::get('task_type/filterCustomers', 'TaskTypeController@filterCustomers')->name('task_type.filterCustomers');
    Route::resource('task_type', 'TaskTypeController');

    Route::get('task/data-table', 'TaskController@dataTable')->name('task.dataTable');
    Route::get('task/filterUsers', 'TaskController@filterUsers')->name('task.filterUsers');
    Route::get('task/filterCustomers', 'TaskController@filterCustomers')->name('task.filterCustomers');
    Route::resource('task', 'TaskController');

    Route::get('supplier/data-table', 'SupplierController@dataTable')->name('supplier.dataTable');
    Route::get('supplier/filterCustomers', 'SupplierController@filterCustomers')->name('supplier.filterCustomers');
    Route::get('supplier/filterProducts/{customer}', 'SupplierController@filterProducts')->name('supplier.filterProducts');
    Route::get('supplier/filterByProduct/{product?}', 'SupplierController@filterByProduct')->name('supplier.filterByProduct');
    Route::get('supplier/getVendorModal/{supplier}', 'SupplierController@getVendorModal')->name('supplier.getVendorModal');
    Route::resource('supplier', 'SupplierController');

    Route::get('profile/webhook/filterUsers', 'WebhookController@filterUsers')->name('webhook.filterUsers');
    Route::resource('profile/webhook', 'WebhookController');

    Route::get('locations/data-table', 'LocationController@dataTable')->name('location.dataTable');
    Route::get('locations/product/data-table', 'LocationController@productLocationDataTable')->name('productLocation.dataTable');
    Route::get('location/types/data-table', 'LocationTypeController@dataTable')->name('locationType.dataTable');
    Route::get('location/filterLocations', 'LocationController@filterLocations')->name('location.filterLocations');
    Route::get('location/filterProducts/{location?}', 'LocationController@filterProducts')->name('location.filterProducts');
    Route::post('location/transfer/product/{location}', 'LocationController@transfer')->name('location.transfer');
    Route::post('location/product/{location}/quantity/update', 'LocationController@updateLocationProductQuantity');
    Route::get('location/getLocationModal/{warehouse?}/{location?}', 'LocationController@getLocationModal')->name('location.getLocationModal');
    Route::post('location/product/import', 'LocationController@importInventory')->name('location.importInventory')->middleware('3pl');
    Route::post('location/product/export', 'LocationController@exportInventory')->name('location.exportInventory');
    Route::get('location/types/filter/{customer?}', 'LocationTypeController@getTypes')->name('location.types');
    Route::post('location/export/csv', 'LocationController@exportCsv')->name('location.exportCsv');
    Route::post('location/import/csv', 'LocationController@importCsv')->name('location.importCsv')->middleware('3pl');

    Route::resource('location', 'LocationController');
    Route::resource('location_type', 'LocationTypeController')->middleware('3pl');

    Route::get('inventory_log/data-table', 'InventoryLogController@dataTable')->name('inventory_log.dataTable');
    Route::post('inventory_log/export', 'InventoryLogController@exportInventory')->name('inventory_log.exportInventory');
    Route::resource('inventory_log', 'InventoryLogController');

    Route::resource('shipping_method', 'ShippingMethodController')->only(['index', 'edit', 'update']);
    Route::get('shipping_method/drop-points', 'ShippingMethodController@getDropPoints')->name('shipping_method.getDropPoints');
    Route::get('shipping_method/data-table', 'ShippingMethodController@dataTable')->name('shipping_method.dataTable');

    Route::get('shipping_method_mapping/data-table', 'ShippingMethodMappingController@dataTable')->name('shipping_method_mapping.dataTable');
    Route::get('shipping_method_mapping/filter-customers', 'ShippingMethodMappingController@filterCustomers')->name('shipping_method_mapping.filterCustomers');
    Route::get('shipping_method_mapping/filter-shipping-methods/{customer?}', 'ShippingMethodMappingController@filterShippingMethods')->name('shipping_method_mapping.filterShippingMethods');
    Route::get('shipping_method_mapping/create/{shipping_method_name?}', 'ShippingMethodMappingController@create')->name('shipping_method_mapping.create');
    Route::resource('shipping_method_mapping', 'ShippingMethodMappingController')->except(['create']);

    Route::get('totes/data-table', 'ToteController@dataTable')->name('tote.dataTable');
    Route::get('totes/tote-items-data-table/{tote}', 'ToteController@toteItemsDataTable')->name('tote.toteItemsDataTable');

    Route::get('totes/filterWarehouses', 'ToteController@filterWarehouses')->name('tote.filterWarehouses');
    Route::get('totes/filterPickingCarts', 'ToteController@filterPickingCarts')->name('tote.filterPickingCarts');
    Route::post('totes/import/csv', 'ToteController@importCsv')->name('tote.importCsv');
    Route::post('totes/export/csv', 'ToteController@exportCsv')->name('tote.exportCsv');
    Route::post('tote/clear/{tote}', 'ToteController@clearItems')->name('tote.clear')->middleware('3pl');
    Route::resource('tote', 'ToteController')->middleware('3pl');

    Route::get('picking_carts/data-table', 'PickingCartController@dataTable')->name('pickingCart.dataTable');
    Route::get('picking_carts/filterWarehouses', 'PickingCartController@filterWarehouses')->name('pickingCart.filterWarehouses');
    Route::get('picking_carts/filterTotes', 'PickingCartController@filterTotes')->name('pickingCart.filterTotes');
    Route::resource('picking_carts', 'PickingCartController')->middleware('3pl');

    Route::get('edit_columns/update', 'EditColumnController@update')->name('editColumn.update');

    Route::post('user_settings/dashboard_settings', 'UserSettingController@dashboardSettingsUpdate')->name('user_settings.dashboard_settings');

    Route::post('user_widgets/save', 'UserWidgetController@createUpdate')->name('user_widgets.save');
    Route::get('user_widgets/get', 'UserWidgetController@getWidgets')->name('user_widgets.get_widgets');
    Route::get('user_widgets/get_sales', 'UserWidgetController@getDashboardSalesWidget')->name('user_widgets.get_dashboard_sales');
    Route::get('user_widgets/get_top_selling_items', 'UserWidgetController@getDashboardTopSellingWidget')->name('user_widgets.get_top_selling_items');
    Route::get('user_widgets/get_info', 'UserWidgetController@getDashboardInfoWidget')->name('user_widgets.get_info');

    Route::get('user_settings/edit', 'UserSettingController@edit')->name('user_settings.edit');
    Route::put('user_settings/update', 'UserSettingController@update')->name('user_settings.update');
    Route::get('settings/manage_users', 'UserController@index')->name('settings.manageUsers');
    Route::get('settings/manage_carriers', 'UserWidgetController@getWidgets')->name('settings.manageCarriers');
    Route::get('settings/manage_stores', 'UserWidgetController@getWidgets')->name('settings.manageStores');
    Route::get('settings/add_card', 'UserWidgetController@getWidgets')->name('settings.addCard');
    Route::get('settings/notifications', 'UserWidgetController@getWidgets')->name('settings.notifications');

    Route::get('tag/filterInputTags', 'TagController@filterInputTags')->name('tag.filterInputTags');

    Route::get('printer', 'PrinterController@index')->name('printer.index')->middleware('3pl');
    Route::get('printer/data-table', 'PrinterController@dataTable')->name('printer.dataTable');
    Route::get('printer/{printer}/disable', 'PrinterController@disable')->name('printer.disable')->middleware('3pl');
    Route::get('printer/{printer}/enable', 'PrinterController@enable')->name('printer.enable')->middleware('3pl');

    Route::get('printer/{printer}/jobs', 'PrinterController@jobs')->name('printer.jobs')->middleware('3pl');
    Route::get('printer/{printer}/jobs-data-table', 'PrinterController@jobsDataTable')->name('printer.jobs.dataTable');
    Route::get('print_job/{printJob}/repeat', 'PrinterController@jobRepeat')->name('printer.job.repeat')->middleware('3pl');

    Route::get('location_layout', 'LocationLayoutController@customerIndex')->name('location_layout.customers.index')->can('viewAny', \App\Models\Customer::class);
    Route::get('location_layout/{customer}/warehouses', 'LocationLayoutController@warehouseIndex')->name('location_layout.warehouse.index')->can('view', 'customer');
    Route::get('location_layout/{warehouse}/locations', 'LocationLayoutController@locationIndex')->name('location_layout.location.index')->can('view', 'warehouse');
    Route::get('location_layout/{location}/products', 'LocationLayoutController@productIndex')->name('location_layout.product.index')->can('view', 'location');

    Route::get('account/settings', 'AccountController@settings')->name('account.settings');
    Route::get('account/settings/{customer}/payment-method', 'AccountController@paymentMethod')->name('account.payment-method');
    Route::get('account/settings/{customer}/billing-details', 'AccountController@billingDetails')->name('account.billing-details');
    Route::get('account/customer/{customer}/invoice/{invoice}', 'AccountController@downloadInvoice')->name('account.download-invoice');
    Route::get('account/upgrade/{customer}', 'PaymentController@upgrade')->name('account.upgrade');
    Route::post('account/cancel-subscription/{customer}', 'PaymentController@cancelSubscription')->name('account.cancel-subscription');
    Route::post('payment/store-method/{customer}', 'PaymentController@storePaymentMethod')->name('payment.storeMethod');
    Route::post('payment/billing-details/{customer}', 'PaymentController@updateBillingDetails')->name('payment.updateBillingDetails');

    Route::get('{page}', ['as' => 'page.index', 'uses' => 'PageController@index']);


    Route::prefix('report')->name('report.')->group( function () {
        Route::get('{reportId}', 'ReportController@view')->name('view');
        Route::get('{reportId}/data_table', 'ReportController@dataTable')->name('dataTable');
        Route::post('{reportId}/export', 'ReportController@export')->name('export');
        Route::get('{reportId}/widgets', 'ReportController@widgets')->name('widgets');
    });

    Route::get('picking_batch/{pickingBatch}/items', 'PickingBatchController@getItems')->withTrashed()->name('picking_batch.getItems');
    Route::get('picking_batch/{pickingBatch}/data_table', 'PickingBatchController@dataTable')->withTrashed()->name('picking_batch.dataTable');
    Route::post('picking_batch/{pickingBatch}/clear_batch', 'PickingBatchController@clearBatch')->name('picking_batch.clearBatch');
});

Route::get('shipment/{shipment}/label/{shipmentLabel}', 'ShipmentController@label')->name('shipment.label');
Route::get('return/{return}/label/{returnLabel}', 'ReturnController@label')->name('return.label');
Route::get('shipment/{shipment}/packing_slip', 'ShipmentController@getPackingSlip')->name('shipment.getPackingSlip');
Route::get('order/{order}/order_slip', 'OrderController@getOrderSlip')->name('order.getOrderSlip');
Route::get('product/{product}/barcode', 'ProductController@barcode')->name('product.barcode');
Route::get('tote/bulk_print/barcodes', 'ToteController@printBarcodes')->name('tote.printBarcodes');
Route::get('tote/{tote}/barcode', 'ToteController@barcode')->name('tote.barcode');
Route::get('picking_carts/{picking_cart}/barcode', 'PickingCartController@barcode')->name('pickingCart.barcode');
