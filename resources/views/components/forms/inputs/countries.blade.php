@props([
    'name' => 'country_of_origin',
    'label' => __('Country of origin'),
    'placeholder' => __('Country of origin'),
    'url' => route('site.filterCountries'),
    'minInputLength' => 0,
    'value' => [],
])

<x-forms.inputs.select-ajax
    :name="$name"
    :label="$label"
    :placeholder="$placeholder"
    :url="$url"
    :min-input-length="$minInputLength"
    :value="$value"
/>