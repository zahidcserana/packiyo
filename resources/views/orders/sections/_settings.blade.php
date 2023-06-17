@include('shared.forms.typeCheckbox', [
    'name' => 'allow_partial',
    'label' => __('Allow partial'),
    'checked' => (! empty(old('allow_partial'))) ? old('allow_partial') :  $order->allow_partial,
    'checkboxFirst' => false,
    'containerClass' => 'd-flex justify-content-between px-2 p-2 font-sm',
    'labelClass' => 'w-100',
    'inputClass' => ''
])

@include('shared.forms.range', [
    'name' => 'priority',
    'label' => __('Priority'),
    'labelClass' => 'form-label',
    'containerClass' => 'px-2 pr-3',
    'class' => 'form-range w-100',
    'min' => -5,
    'max' => 5,
    'step' => 1,
    'value' => $order->priority,
    'defaultValue' => 0,
])

@include('shared.forms.typeCheckbox', [
    'name' => 'gift_invoice',
    'label' => __('Gift invoice'),
    'checked' => (! empty(old('gift_invoice'))) ? old('gift_invoice') :  $order->gift_invoice,
    'checkboxFirst' => false,
    'containerClass' => 'd-flex justify-content-between px-2 p-2 font-sm',
    'labelClass' => 'w-100',
    'inputClass' => ''
])
@include('shared.forms.typeCheckbox', [
    'name' => 'operator_hold',
    'label' => __('Operator hold'),
    'checked' => (! empty(old('operator_hold'))) ? old('operator_hold') :  $order->operator_hold,
    'checkboxFirst' => false,
    'containerClass' => 'd-flex justify-content-between px-2 p-2 font-sm',
    'labelClass' => 'w-100',
    'inputClass' => ''
])
@include('shared.forms.typeCheckbox', [
    'name' => 'payment_hold',
    'label' => __('Payment hold'),
    'checked' => (! empty(old('payment_hold'))) ? old('payment_hold') :  $order->payment_hold,
    'checkboxFirst' => false,
    'containerClass' => 'd-flex justify-content-between px-2 p-2 font-sm',
    'labelClass' => 'w-100',
    'inputClass' => ''
])
@include('shared.forms.typeCheckbox', [
    'name' => 'address_hold',
    'label' => __('Address hold'),
    'checked' => (! empty(old('address_hold'))) ? old('address_hold') :  $order->address_hold,
    'checkboxFirst' => false,
    'containerClass' => 'd-flex justify-content-between px-2 p-2 font-sm',
    'labelClass' => 'w-100',
    'inputClass' => ''
])
@include('shared.forms.typeCheckbox', [
    'name' => 'fraud_hold',
    'label' => __('Fraud hold'),
    'checked' => (! empty(old('fraud_hold'))) ? old('fraud_hold') :  $order->fraud_hold,
    'checkboxFirst' => false,
    'containerClass' => 'd-flex justify-content-between px-2 p-2 font-sm',
    'labelClass' => 'w-100',
    'inputClass' => ''
])
@include('shared.forms.typeCheckbox', [
    'name' => 'allocation_hold',
    'label' => __('Allocation hold'),
    'checked' => (! empty(old('allocation_hold'))) ? old('allocation_hold') :  $order->allocation_hold,
    'checkboxFirst' => false,
    'containerClass' => 'd-flex justify-content-between px-2 p-2 font-sm',
    'labelClass' => 'w-100',
    'inputClass' => ''
])
