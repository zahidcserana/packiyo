@extends('layouts.app')

@section('content')
    @component('layouts.headers.auth', [ 'title' => 'Settings', 'subtitle' => 'General'])
    @endcomponent
    <div class="container-fluid formsContainer">
        <div class="row px-3" id="globalForm" data-action="{{ route('user_settings.update') }}" data-type="PUT">
            <form class="col-12 border-12 py-3 px-4 m-0 mb-3 bg-white smallForm min-h-100vh settingsForm"
                  action="{{ route('user_settings.update') }}" data-type="PUT" enctype="multipart/form-data">
                <div class="border-bottom py-2 d-flex align-items-center">
                    <h6 class="modal-title text-black text-left"
                        id="modal-title-notification"></h6>
                    @include('shared.buttons.smallFormButtons', ['saveButtonId' => 'submit-general-settings'])
                </div>
                @csrf
                {{ method_field('PUT') }}
                <div class="d-flex text-center py-3 overflow-auto justify-content-between flex-column">
                    <div class="w-100">
                        @include('shared.forms.editSelect', [
                            'label' => __('Timezone'),
                            'name' => \App\Models\UserSetting::USER_SETTING_TIMEZONE,
                            'value' => user_settings(\App\Models\UserSetting::USER_SETTING_TIMEZONE),
                            'options' => $timezones
                        ])
                        @include('shared.forms.editSelect', [
                            'label' => __('Date Format'),
                            'name' => \App\Models\UserSetting::USER_SETTING_DATE_FORMAT,
                            'value' => user_settings(\App\Models\UserSetting::USER_SETTING_DATE_FORMAT),
                            'options' => $dateFormats
                        ])
                        @include('shared.forms.editSelect', [
                            'label' => __('Label Printer'),
                            'name' => \App\Models\UserSetting::USER_SETTING_LABEL_PRINTER_ID,
                            'value' => user_settings(\App\Models\UserSetting::USER_SETTING_LABEL_PRINTER_ID),
                            'options' => $printers
                        ])
                        @include('shared.forms.editSelect', [
                            'label' => __('Barcode Printer'),
                            'name' => \App\Models\UserSetting::USER_SETTING_BARCODE_PRINTER_ID,
                            'value' => user_settings(\App\Models\UserSetting::USER_SETTING_BARCODE_PRINTER_ID),
                            'options' => $printers
                        ])
                        @include('shared.forms.editSelect', [
                            'label' => __('Slip Printer'),
                            'name' => \App\Models\UserSetting::USER_SETTING_SLIP_PRINTER_ID,
                            'value' => user_settings(\App\Models\UserSetting::USER_SETTING_SLIP_PRINTER_ID),
                            'options' => $printers
                        ])
                    </div>
                </div>
            </form>
        </div>
    </div>
@endsection

@push('js')
    <script>
        new Settings();
    </script>
@endpush

