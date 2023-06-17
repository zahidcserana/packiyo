@if(!isset($shippingBox->customer) && !isset($sessionCustomer))
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
    <input type="hidden" name="customer_id" value="{{ $shippingBox->customer->id ?? $sessionCustomer->id }}" class="customer_id" />
@endif

<div class="row">
    <div class="col-6">
        @include('shared.forms.input', [
        'name' => 'name',
        'label' => __('Name'),
        'value' => $shippingBox->name ?? ''
        ])
    </div>
</div>
<div class="row">
    <div class="col-6">
        @include('shared.forms.input', [
            'name' => 'length',
            'label' => __('Length'),
            'value' => $shippingBox->length ?? ''
        ])
    </div>
    <div class="col-6 pt-4">
        @include('shared.forms.editCheckbox', [
            'name' => 'length_locked',
            'label' => __('Length locked'),
            'checked' => (! empty(old('length_locked'))) ? old('length_locked') :  ($shippingBox->length_locked ?? ''),
            'noCenter' => true,
            'noYes' => true,
            'noBorder' => true,
            'checkboxFirst' => true
        ])
    </div>
</div>
<div class="row">
    <div class="col-6">
        @include('shared.forms.input', [
            'name' => 'width',
            'label' => __('Width'),
            'value' => $shippingBox->width ?? ''
        ])
    </div>
    <div class="col-6 pt-4">
        @include('shared.forms.editCheckbox', [
            'name' => 'width_locked',
            'label' => __('Width locked'),
            'checked' => (! empty(old('width_locked'))) ? old('width_locked') :  ($shippingBox->width_locked ?? ''),
            'noCenter' => true,
            'noYes' => true,
            'noBorder' => true,
            'checkboxFirst' => true
        ])
    </div>
</div>
<div class="row">
    <div class="col-6">
        @include('shared.forms.input', [
            'name' => 'height',
            'label' => __('Height'),
            'value' => $shippingBox->height ?? ''
        ])
    </div>
    <div class="col-6 pt-4">
        @include('shared.forms.editCheckbox', [
            'name' => 'height_locked',
            'label' => __('Height locked'),
            'checked' => (! empty(old('height_locked'))) ? old('height_locked') :  ($shippingBox->height_locked ?? ''),
            'noCenter' => true,
            'noYes' => true,
            'noBorder' => true,
            'checkboxFirst' => true
        ])
    </div>
</div>
