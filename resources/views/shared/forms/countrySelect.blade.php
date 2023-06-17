@include('shared.forms.select', [
    'label' => $label ?? __('Country'),
    'placeholder' => __('Select country'),
    'options' => ['' => ''] + $countries
])
