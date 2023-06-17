@include('shared.forms.input', [
    'name' => $name . '[api_base_url]',
    'label' => __('Api Base Url'),
    'value' => $easypostCredentials->api_base_url ?? ''
])

@include('shared.forms.input', [
    'name' => $name . '[api_key]',
    'label' => __('Api Key'),
    'value' => $easypostCredentials->api_key ?? ''
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
    'value' => $easypostCredentials->id ?? ''
])
