<div class="d-flex flex-column">
    <div class="d-lg-flex">
        @include('shared.forms.input', [
            'name' => \App\Models\CustomerSetting::CUSTOMER_SETTING_LABEL_SIZE_WIDTH,
            'label' => __('Label width (:dimension)', ['dimension' => $settings[\App\Models\CustomerSetting::CUSTOMER_SETTING_DIMENSIONS_UNIT] ?? 'mm']),
            'containerClass' => 'w-50 mx-2',
            'error' => ! empty($errors->get(\App\Models\CustomerSetting::CUSTOMER_SETTING_LABEL_SIZE_WIDTH)) ? $errors->first(\App\Models\CustomerSetting::CUSTOMER_SETTING_LABEL_SIZE_WIDTH) : false,
            'value' => $settings[\App\Models\CustomerSetting::CUSTOMER_SETTING_LABEL_SIZE_WIDTH] ?? ''
        ])
        @include('shared.forms.input', [
            'name' => \App\Models\CustomerSetting::CUSTOMER_SETTING_DOCUMENT_SIZE_WIDTH,
            'label' => __('Document width (:dimension)', ['dimension' => $settings[\App\Models\CustomerSetting::CUSTOMER_SETTING_DIMENSIONS_UNIT] ?? 'mm']),
            'containerClass' => 'w-50 mx-2',
            'error' => ! empty($errors->get(\App\Models\CustomerSetting::CUSTOMER_SETTING_DOCUMENT_SIZE_WIDTH)) ? $errors->first(\App\Models\CustomerSetting::CUSTOMER_SETTING_DOCUMENT_SIZE_WIDTH) : false,
            'value' => $settings[\App\Models\CustomerSetting::CUSTOMER_SETTING_DOCUMENT_SIZE_WIDTH] ?? ''
        ])
        @include('shared.forms.input', [
            'name' => \App\Models\CustomerSetting::CUSTOMER_SETTING_BARCODE_SIZE_WIDTH,
            'label' => __('Barcode width (:dimension)', ['dimension' => $settings[\App\Models\CustomerSetting::CUSTOMER_SETTING_DIMENSIONS_UNIT] ?? 'mm']),
            'containerClass' => 'w-50 mx-2',
            'error' => ! empty($errors->get(\App\Models\CustomerSetting::CUSTOMER_SETTING_BARCODE_SIZE_WIDTH)) ? $errors->first(\App\Models\CustomerSetting::CUSTOMER_SETTING_BARCODE_SIZE_WIDTH) : false,
            'value' => $settings[\App\Models\CustomerSetting::CUSTOMER_SETTING_BARCODE_SIZE_WIDTH] ?? ''
        ])
    </div>
</div>
<div class="d-flex flex-column">
    <div class="d-lg-flex">
        @include('shared.forms.input', [
            'name' => \App\Models\CustomerSetting::CUSTOMER_SETTING_LABEL_SIZE_HEIGHT,
            'label' => __('Label height (:dimension)', ['dimension' => $settings[\App\Models\CustomerSetting::CUSTOMER_SETTING_DIMENSIONS_UNIT] ?? 'mm']),
            'containerClass' => 'w-50 mx-2',
            'error' => ! empty($errors->get(\App\Models\CustomerSetting::CUSTOMER_SETTING_LABEL_SIZE_HEIGHT)) ? $errors->first(\App\Models\CustomerSetting::CUSTOMER_SETTING_LABEL_SIZE_HEIGHT) : false,
            'value' => $settings[\App\Models\CustomerSetting::CUSTOMER_SETTING_LABEL_SIZE_HEIGHT] ?? ''
        ])
        @include('shared.forms.input', [
            'name' => \App\Models\CustomerSetting::CUSTOMER_SETTING_DOCUMENT_SIZE_HEIGHT,
            'label' => __('Document height (:dimension)', ['dimension' => $settings[\App\Models\CustomerSetting::CUSTOMER_SETTING_DIMENSIONS_UNIT] ?? 'mm']),
            'containerClass' => 'w-50 mx-2',
            'error' => ! empty($errors->get(\App\Models\CustomerSetting::CUSTOMER_SETTING_DOCUMENT_SIZE_HEIGHT)) ? $errors->first(\App\Models\CustomerSetting::CUSTOMER_SETTING_DOCUMENT_SIZE_HEIGHT) : false,
            'value' => $settings[\App\Models\CustomerSetting::CUSTOMER_SETTING_DOCUMENT_SIZE_HEIGHT] ?? ''
        ])
        
        @include('shared.forms.input', [
            'name' => \App\Models\CustomerSetting::CUSTOMER_SETTING_BARCODE_SIZE_HEIGHT,
            'label' => __('Barcode height (:dimension)', ['dimension' => $settings[\App\Models\CustomerSetting::CUSTOMER_SETTING_DIMENSIONS_UNIT] ?? 'mm']),
            'containerClass' => 'w-50 mx-2',
            'error' => ! empty($errors->get(\App\Models\CustomerSetting::CUSTOMER_SETTING_BARCODE_SIZE_HEIGHT)) ? $errors->first(\App\Models\CustomerSetting::CUSTOMER_SETTING_BARCODE_SIZE_HEIGHT) : false,
            'value' => $settings[\App\Models\CustomerSetting::CUSTOMER_SETTING_BARCODE_SIZE_HEIGHT] ?? ''
        ])
    </div>
</div>
