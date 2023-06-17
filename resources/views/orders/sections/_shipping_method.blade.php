@include('shared.forms.select', [
    'name' => 'shipping_method_id',
    'label' => '',
    'className' => 'shipping_method_id',
    'placeholder' => __('Select a shipment method'),
    'error' => !empty($errors->get('shipping_method_id')) ? $errors->first('shipping_method_id') : false,
    'value' => $order->shipping_method_id ?? old('shipping_method_id'),
    'options' => ['dummy' => __('Dummy')] + $shippingMethods->pluck('carrierNameAndName', 'id')->toArray()
])
