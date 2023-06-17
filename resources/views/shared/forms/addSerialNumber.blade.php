<div class="d-flex orderContactInfo flex-column">
    <div class="d-lg-flex" id="modal_serial_number">
        @include('shared.forms.input', [
            'name' => $name,
            'containerClass' => 'w-100',
            'label' => '',
            'placeholder' => 'Scan or type serial number',
            'error' => ! empty($errors->get($name)) ? $errors->first($name) : false,
            'labelClass' => 'justify-content-center d-flex',
        ])
    </div>
</div>
