<div class="d-flex">
    @if(!isset($sessionCustomer))
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
        <input type="hidden" name="customer_id" value="{{ $sessionCustomer->id }}" class="customer_id" />
    @endif
    <div class="w-50">
        @include('shared.forms.input', [
            'name' => 'name',
            'label' => __('Name'),
            'value' => $orderStatus->name ?? ''
        ])
    </div>
    <div class="w-50">
        @include('shared.forms.colorPicker', [
            'name' => 'color',
            'label' => __('Choose status color'),
            'value' => $orderStatus->color ?? ''
        ])
    </div>
</div>
