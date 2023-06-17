@if(!isset($shippingMethodMapping->customer) && !isset($sessionCustomer))
    <div class="searchSelect">
        @include('shared.forms.new.ajaxSelect', [
        'url' => route('user.getCustomers'),
        'name' => 'customer_id',
        'className' => 'ajax-user-input customer_id',
        'placeholder' => __('Select customer'),
        'label' => __('Customer'),
        'default' => [
            'id' => old('customer_id'),
            'text' => ''
        ],
        'fixRouteAfter' => '.ajax-user-input.customer_id'
    ])
    </div>
@else
    <input type="hidden" name="customer_id" value="{{ $shippingMethodMapping->customer->id ?? $sessionCustomer->id }}" class="customer_id" />
@endif

@include('shared.forms.input', [
    'name' => 'shipping_method_name',
    'label' => __('Shop Shipping Method Name'),
    'value' => $shippingMethodMapping->shipping_method_name ?? $shipping_method_name,
    'readOnly' => 'readonly'
])

@include('shared.forms.ajaxSelect', [
    'url' => route('shipping_method_mapping.filterShippingMethods', ['customer' => $shippingMethodMapping->customer->id ?? $sessionCustomer->id ?? 1]),
    'name' => 'shipping_method_id',
    'className' => 'ajax-user-input enabled-for-customer shipping_method_id',
    'placeholder' => __('Search'),
    'label' => __('Shipping Method'),
    'default' => [
        'id' => $shippingMethodMapping->shippingMethod->id ?? old('shipping_method_id'),
        'text' => isset($shippingMethodMapping) ? $shippingMethodMapping->shippingMethod->shippingCarrier->name . ' ' . $shippingMethodMapping->shippingMethod->name : ''
    ],
    'fixRouteAfter' => '.ajax-user-input.customer_id'
])

@include('shared.forms.ajaxSelect', [
    'url' => route('shipping_method_mapping.filterShippingMethods', ['customer' => $shippingMethodMapping->customer->id ?? $sessionCustomer->id ?? 1]),
    'name' => 'return_shipping_method_id',
    'className' => 'ajax-user-input enabled-for-customer shipping_method_id',
    'placeholder' => __('Search'),
    'label' => __('Return Shipping Method'),
    'allowClear' => true,
    'default' => [
        'id' => $shippingMethodMapping->shippingMethod->id ?? old('shipping_method_id'),
        'text' => isset($shippingMethodMapping) ? $shippingMethodMapping->returnShippingMethod->shippingCarrier->name . ' ' . $shippingMethodMapping->returnShippingMethod->name : ''
    ],
    'fixRouteAfter' => '.ajax-user-input.customer_id'
])
