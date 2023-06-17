<div class="d-flex flex-column">
    <div class="row">
        @include('shared.forms.input', [
            'name' => $name . '[name]',
            'containerClass' => 'col-12 col-md-6',
            'label' => __('Name'),
            'error' => ! empty($errors->get($name.'.name')) ? $errors->first($name.'.name') : false,
            'value' => $contactInformation->name ?? ''
        ])
        @include('shared.forms.input', [
          'name' => $name . '[company_name]',
          'containerClass' => 'col-12 col-md-6',
          'label' => __('Company Name'),
          'error' => ! empty($errors->get($name.'company_name')) ? $errors->first($name.'.company_name') : false,
          'value' => $contactInformation->company_name ?? ''
       ])
        @include('shared.forms.input', [
          'name' => $name . '[address]',
          'containerClass' => 'col-12 col-md-6',
          'label' => __('Address'),
          'error' => ! empty($errors->get($name.'.address')) ? $errors->first($name.'.address') : false,
          'value' => $contactInformation->address ?? ''
        ])
        @include('shared.forms.input', [
            'name' => $name . '[address2]',
            'containerClass' => 'col-12 col-md-6',
            'label' => __('Address 2'),
            'error' => ! empty($errors->get($name.'.address2')) ? $errors->first($name.'.address2') : false,
            'value' => $contactInformation->address2 ?? ''
        ])
        @include('shared.forms.input', [
            'name' => $name . '[zip]',
            'containerClass' => 'col-12 col-md-6',
            'label' => __('Zip'),
            'error' => ! empty($errors->get($name.'.zip')) ? $errors->first($name.'.zip') : false,
            'value' => $contactInformation->zip ?? $value ?? ''
        ])
        @include('shared.forms.input', [
            'name' => $name . '[city]',
            'containerClass' => 'col-12 col-md-6',
            'label' => __('City'),
            'error' => ! empty($errors->get($name.'.city')) ? $errors->first($name.'.city') : false,
            'value' => $contactInformation->city ?? $value ?? ''
        ])
        @include('shared.forms.input', [
            'name' => $name . '[state]',
            'containerClass' => 'col-12 col-md-6',
            'label' => __('State'),
            'type' => 'text',
            'error' => ! empty($errors->get($name.'.state')) ? $errors->first($name.'.state') : false,
            'value' => $contactInformation->state ?? $value ?? ''
        ])
        @include('shared.forms.countrySelect', [
            'name' => $name . '[country_id]',
            'containerClass' => 'col-12 col-md-6',
            'error' => !empty($errors->get($name . '.country_id')) ? $errors->first($name . '.country_id') : false,
            'value' => $contactInformation->country_id ?? ''
        ])
        @include('shared.forms.input', [
            'name' => $name . '[email]',
            'containerClass' => 'col-12 col-md-6',
            'label' => __('Contact Email'),
            'type' => 'text',
            'error' => ! empty($errors->get($name.'.email')) ? $errors->first($name.'.email') : false,
            'value' => $contactInformation->email ?? $value ?? ''
        ])
        @include('shared.forms.input', [
            'name' => $name . '[phone]',
            'containerClass' => 'col-12 col-md-6',
            'label' => __('Phone'),
            'error' => ! empty($errors->get($name.'.phone')) ? $errors->first($name.'.phone') : false,
            'value' => $contactInformation->phone ?? $value ?? ''
        ])
        @include('shared.forms.input', [
            'name' => $name . '[company_number]',
            'containerClass' => 'col-12 col-md-6',
            'label' => __('Company Number'),
            'error' => ! empty($errors->get($name.'.company_number')) ? $errors->first($name.'.company_number') : false,
            'value' => $contactInformation->company_number ?? ''
        ])
    </div>
</div>
