@php
    $name = 'contact_information';
    $contactInformation = $supplier->contactInformation ?? '';
@endphp
<div class="d-lg-flex orderContactInfo">
    <div class="flex-grow-1">
        @include('shared.forms.input', [
            'name' => $name . '[name]',
            'dataId' => $name . '.name',
            'label' => __('Name'),
            'value' => $contactInformation->name ?? ''
        ])
        @include('shared.forms.input', [
            'name' => $name . '[address2]',
            'dataId' => $name . '.address2',
            'label' => __('Address 2'),
            'value' => $contactInformation->address2 ?? ''
        ])
        @include('shared.forms.countrySelect', [
            'name' => $name . '[country_id]',
            'containerClass' => 'mb-0 mx-2 text-left mb-3',
            'value' => $contactInformation->country_id ?? ''
        ])
        @include('shared.forms.new.select', [
           'name' => 'currency',
           'dataId' => 'currency_id',
           'label' => __('Currency'),
           'value' => $supplier->currency ?? '',
           'options' => Webpatser\Countries\Countries::all()->pluck('currency_code', 'currency_code'),
           'attributes' => [
                'data-no-select2' => true,
           ]
        ])
    </div>
    <div class="flex-grow-1">
        @include('shared.forms.input', [
           'name' => $name . '[company_name]',
           'dataId' => $name . '.company_name',
           'label' => __('Company Name'),
           'value' => $contactInformation->company_name ?? ''
        ])
        @include('shared.forms.input', [
            'name' => $name . '[zip]',
            'dataId' => $name . '.zip',
            'label' => __('Zip'),
            'value' => $contactInformation->zip ?? $value ?? ''
        ])
        @include('shared.forms.input', [
            'name' => $name . '[email]',
            'dataId' => $name . '.email',
            'label' => __('Contact Email'),
            'type' => 'email',
            'value' => $contactInformation->email ?? $value ?? ''
        ])
        @include('shared.forms.input', [
           'name' => 'internal_note',
           'label' => __('Internal note'),
           'value' => $supplier->internal_note ?? ''
       ])
    </div>
    <div class="flex-grow-1">
        @include('shared.forms.input', [
            'name' => $name . '[address]',
            'dataId' => $name . '.address',
            'label' => __('Address'),
            'value' => $contactInformation->address ?? ''
        ])
        @include('shared.forms.input', [
             'name' => $name . '[city]',
             'dataId' => $name . '.city',
             'label' => __('City'),
             'value' => $contactInformation->city ?? $value ?? ''
         ])
        @include('shared.forms.input', [
            'name' => $name . '[phone]',
            'dataId' => $name . '.phone',
            'label' => __('Phone'),
            'value' => $contactInformation->phone ?? $value ?? ''
        ])
        @include('shared.forms.input', [
            'name' => 'default_purchase_order_note',
            'label' => __('Default PO note'),
            'value' => $supplier->default_purchase_order_note ?? ''
        ])
    </div>
</div>

