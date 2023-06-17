<div class=" {{ $checkboxForFillingInformation ? 'col-xl-6' : 'col-xl-4'}} sizing">
    <h6 class="heading-small text-muted mb-4">{{ __('Order information') }}</h6>
        @include('shared.forms.input', [
           'name' => 'number',
           'label' => __('Number'),
           'value' => $order->number ?? '',
           'readOnly' => $order->number ?? false ? 'readonly' : ''
        ])
        <div class="form-group">
            <label class="form-control-label">{{ __('Order Priority') }}</label>
            <select name="priority" class="form-control enabled-for-customer" data-toggle="select" data-placeholder="">
                @for( $i =1; $i <= 5; $i++ )
                    <option value="{{$i}}" @if($i == ($order->priority ?? null)) selected @endif >{{$i}}</option>
                @endfor
            </select>
        </div>
        @include('shared.forms.input', [
           'name' => 'shipping',
           'label' => __('Shipping Cost'),
           'value' => $order->shipping ?? 0,
        ])
        @include('shared.forms.input', [
           'name' => 'tax',
           'label' => __('Tax'),
           'value' => $order->tax ?? 0
        ])
        @include('shared.forms.input', [
           'name' => 'subtotal',
           'label' => __('Subtotal'),
           'value' => $order->subtotal ?? 0,
           'readOnly' => 'readonly'
        ])
        @include('shared.forms.input', [
           'name' => 'total',
           'label' => __('Total'),
           'value' => $order->total ?? 0,
           'readOnly' => 'readonly'
        ])
        @include('shared.forms.textarea', [
           'name' => 'notes',
           'label' => __('Notes'),
           'value' => old('notes') ?? $order->notes ?? '',
        ])
        @include('shared.forms.textarea', [
           'name' => 'gift_note',
           'label' => __('Gift notes'),
           'value' => old('gift_note') ?? $order->gift_note ?? '',
        ])
        <div class="form-group">
            <label>Order hold</label>
            @include('shared.forms.checkbox', [
                'name' => 'address_hold',
                'label' => __('Address hold'),
                'checked' => old('address_hold') ?? $order->address_hold ?? false,
            ])
            @include('shared.forms.checkbox', [
                'name' => 'fraud_hold',
                'label' => __('Fraud hold'),
                'checked' => old('fraud_hold') ?? $order->fraud_hold ?? false,
            ])
            @include('shared.forms.checkbox', [
                'name' => 'operator_hold',
                'label' => __('Operator hold'),
                'checked' =>  old('operator_hold') ?? $order->operator_hold ?? false,
            ])
            @include('shared.forms.checkbox', [
                'name' => 'payment_hold',
                'label' => __('Payment hold'),
                'checked' => old('payment_hold') ?? $order->payment_hold ?? false,
            ])
    </div>
    @include('shared.forms.checkbox', [
        'name' => 'allow_partial',
        'label' => __('Allow partial'),
        'checked' => old('allow_partial') ?? $order->allow_partial ?? false,
    ])
</div>
<div class="{{ $checkboxForFillingInformation ? 'col-xl-6' : 'col-xl-4'}} sizing">
    <h6 class="heading-small text-muted mb-4">{{ __('Shipping information') }}</h6>
    @include('shared.forms.contactInformationFields', [
        'name' => 'shipping_contact_information',
        'contactInformation' => $order->shippingContactInformation ?? ''
    ])
    @if($checkboxForFillingInformation)
        <input type="checkbox" id="fill-information" name="differentBillingInformation">
        <label for="fill-information">{{ __('Different billing information') }}</label>
    @else

    @endif

</div>
<div class="col-xl-4 billing_contact_information">
    <h6 class="heading-small text-muted mb-4">{{ __('Billing information') }}</h6>
    @include('shared.forms.contactInformationFields', [
        'name' => 'billing_contact_information',
        'contactInformation' => $order->billingContactInformation ?? ''
    ])
</div>
