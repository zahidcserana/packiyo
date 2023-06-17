<div class="d-flex orderContactInfo flex-column mb-4">
    <div class="d-lg-flex">
        @include('shared.forms.select', [
           'name' => 'weight_unit',
           'label' => __('Weight unit'),
           'containerClass' => 'w-50 mx-2',
           'value' => $settings['weight_unit'] ?? 'kg',
           'options' => \App\Models\Customer::WEIGHT_UNITS
       ])

        @include('shared.forms.select', [
            'name' => 'dimensions_unit',
            'label' => __('Dimension unit'),
           'containerClass' => 'w-50 mx-2',
            'value' => $settings['dimensions_unit'] ?? 'cm',
            'options' => \App\Models\Customer::DIMENSION_UNITS
        ])
    </div>
</div>
