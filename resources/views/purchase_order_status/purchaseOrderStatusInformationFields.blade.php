@include('shared.forms.dropdowns.customer_selection', [
    'route' => route('purchase_order_status.filterCustomers'),
    'readonly' => isset($purchaseOrderStatus->customer->id) ? 'true' : null,
    'id' => $purchaseOrderStatus->customer->id ?? old('customer_id'),
    'text' => $purchaseOrderStatus->customer->contactInformation->name ?? ''
])
@include('shared.forms.input', [
    'name' => 'name',
    'label' => __('Name'),
    'value' => $purchaseOrderStatus->name ?? ''
])
