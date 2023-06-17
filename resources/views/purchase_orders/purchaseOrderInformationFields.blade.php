<div class="col-xl-6 sizing">
    @include('shared.forms.dropdowns.customer_selection', [
        'route' => route('purchase_order.filterCustomers'),
        'readonly' => isset($purchaseOrder->customer->id) ? 'true' : null,
        'id' => $purchaseOrder->customer->id ?? old('customer_id'),
        'text' => $purchaseOrder->customer->contactInformation->name ?? ''
    ])
    @include('shared.forms.ajaxSelect', [
        'url' => route('purchase_order.filterWarehouses', ['customer' => $purchaseOrder->customer->id ?? 1]),
        'name' => 'warehouse_id',
        'className' => 'ajax-user-input warehouse_id enabled-for-customer',
        'placeholder' => __('Search'),
        'label' => __('Warehouse'),
        'default' => [
            'id' => $purchaseOrder->warehouse->id ?? old('warehouse_id'),
            'text' => $purchaseOrder->warehouse->contactInformation->name ?? ''
        ],
        'fixRouteAfter' => '.ajax-user-input.customer_id'
    ])
    @include('shared.forms.ajaxSelect', [
        'url' => route('purchase_order.filterSuppliers',  ['customer' => $purchaseOrder->customer->id ?? 1]),
        'name' => 'supplier_id',
        'className' => 'ajax-user-input supplier_id enabled-for-customer',
        'placeholder' => __('Enter Vendor'),
        'label' => __('Vendor'),
        'default' => [
            'id' => $purchaseOrder->supplier->id ?? old('supplier_id'),
            'text' => $purchaseOrder->supplier->contactInformation->name ?? ''
        ],
        'fixRouteAfter' => '.ajax-user-input.customer_id'
    ])
    @include('shared.forms.input', [
       'name' => 'number',
       'label' => __('PO Number'),
       'value' => $purchaseOrder->number ?? ''
    ])
    @include('shared.forms.editSelectTag', [
        'containerClass' => 'form-group mb-0 mx-2 text-left mb-3',
        'labelClass' => '',
        'selectClass' => 'select-ajax-tags',
        'label' => __('Tags'),
        'minimumInputLength' => 3,
        'default' => $purchaseOrder->tags ?? []
    ])
</div>
<div class="col-xl-6 sizing">
    <div class="form-group mb-0 mx-2 text-left mb-3">
        <label for=""
               data-id="tracking_number"
               class="text-neutral-text-gray font-weight-600 font-xs">{{ __('Tracking number') }} </label>
        <div
            class="input-group input-group-alternative input-group-merge">
            <input
                class="form-control font-weight-600 text-neutral-gray h-auto p-2"
                placeholder="{{ __('Tracking number') }}"
                type="text"
                name="tracking_number"
                value="{{ $purchaseOrder->tracking_number ?? '' }}"
            >
        </div>
    </div>
    <div class="form-group mb-0 mx-2 text-left mb-3">
        <label for=""
               data-id="tracking_url"
               class="text-neutral-text-gray font-weight-600 font-xs">{{ __('Tracking URL') }} </label>
        <div
            class="input-group input-group-alternative input-group-merge">
            <input
                class="form-control font-weight-600 text-neutral-gray h-auto p-2"
                placeholder="{{ __('Tracking URL') }}"
                type="text"
                name="tracking_url"
                value="{{ $purchaseOrder->tracking_url ?? '' }}"
            >
        </div>
    </div>
    <div class="form-group mb-0 mx-2 text-left mb-3">
        <label for=""
               data-id="ordered_at"
               class="text-neutral-text-gray font-weight-600 font-xs">{{ __('Ordered at') }} </label>
        <div
            class="input-group input-group-alternative input-group-merge">
            <input
                class="form-control font-weight-600 text-neutral-gray h-auto p-2  datetimepicker"
                placeholder="{{ __('Ordered at') }}"
                type="text"
                name="ordered_at"
                value="{{ $purchaseOrder->ordered_at ?? '' }}"
            >
        </div>
    </div>
    <div class="form-group mb-0 mx-2 text-left mb-3">
        <label for=""
               data-id="expected_at"
               class="text-neutral-text-gray font-weight-600 font-xs">{{ __('Expected at') }} </label>
        <div
            class="input-group input-group-alternative input-group-merge">
            <input
                class="form-control font-weight-600 text-neutral-gray h-auto p-2 datetimepicker"
                placeholder="{{ __('Expected at') }}"
                type="text"
                name="expected_at"
                value="{{ $purchaseOrder->expected_at ?? '' }}"
            >
        </div>
    </div>
</div>
