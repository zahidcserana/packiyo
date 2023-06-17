@include('shared.forms.input', [
    'name' => $name . '[api_base_url]',
    'label' => __('Api Base Url'),
    'value' => $webshipperCredentials->api_base_url ?? ''
])

@include('shared.forms.input', [
    'name' => $name . '[api_key]',
    'label' => __('Api Key'),
    'value' => $webshipperCredentials->api_key ?? ''
])

@include('shared.forms.input', [
    'name' => $name . '[order_channel_id]',
    'label' => __('Order Channel Id'),
    'type'=> 'number',
    'value' => $webshipperCredentials->order_channel_id ?? ''
])

@include('shared.forms.input', [
    'name' => $name . '[customer_id]',
    'label' => __(''),
    'type'=> 'hidden',
    'value' => $customer->id ?? ''
])

@include('shared.forms.input', [
    'name' => $name . '[id]',
    'label' => __(''),
    'type'=> 'hidden',
    'value' => $webshipperCredentials->id ?? ''
])
