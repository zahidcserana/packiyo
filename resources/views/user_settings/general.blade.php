@extends('layouts.app')

@section('content')
    @component('layouts.headers.auth', [ 'title' => 'Settings', 'subtitle' => 'General'])
    @endcomponent
    <div class="container-fluid formsContainer">
        <div class="row px-3" id="globalForm" data-action="{{ route('settings.general.update') }}"  data-type="PUT">
            <form class="col-12 border-12 py-3 px-4 m-0 mb-3 bg-white smallForm min-h-100vh settingsForm" action="{{ route('settings.general.update') }}"  data-type="PUT" enctype="multipart/form-data">
                <div class="border-bottom py-2 d-flex align-items-center">
                    <h6 class="modal-title text-black text-left"
                        id="modal-title-notification"></h6>
                    @include('shared.buttons.smallFormButtons', ['saveButtonId' => 'submit-general-settings'])
                    @csrf
                    {{ method_field('PUT') }}
                </div>
                <div class="d-flex text-center py-3 overflow-auto justify-content-between flex-column">
                    <div class="w-100">
                        @include('shared.forms.editSelect', [
                            'label' => __('Timezone'),
                            'name' => 'timezone',
                            'value' => $settings['timezone'] ?? '',
                            'options' => $timezones
                        ])

                        @include('shared.forms.editSelect', [
                            'label' => __('Date Format'),
                            'name' => 'date_format',
                            'value' => $settings['date_format'] ?? '',
                            'options' => $dateFormats
                        ])

                        @include('shared.forms.editSelect', [
                            'label' => __('Label Printer'),
                            'name' => 'label_printer',
                            'value' => $settings['label_printer'] ?? '',
                            'options' => $printers
                        ])

                        @include('shared.forms.editSelect', [
                            'label' => __('Barcode Printer'),
                            'name' => 'barcode_printer',
                            'value' => $settings['barcode_printer'] ?? '',
                            'options' => $printers
                        ])

                        @include('shared.forms.editSelect', [
                            'label' => __('Order Slip Printer'),
                            'name' => 'order_slip_printer',
                            'value' => $settings['order_slip_printer'] ?? '',
                            'options' => $printers
                        ])

                        @include('shared.forms.editSelect', [
                            'label' => __('Packing Slip Printer'),
                            'name' => 'packing_slip_printer',
                            'value' => $settings['packing_slip_printer'] ?? '',
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

