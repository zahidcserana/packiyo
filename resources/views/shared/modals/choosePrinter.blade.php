<div class="modal fade confirm-dialog" id="choosePrinter" tabindex="-1" role="dialog">
    <div class="modal-dialog modal-dialog-centered" role="document">
        <div class="modal-content">
            <div class="modal-header px-0">
                <div class="mx-4 pb-4 d-flex w-100">
                    <h6 class="modal-title text-black text-left"
                        id="modal-title-notification">{{ __('Choose the printer') }}</h6>
                    <button type="button" class="close" data-dismiss="modal" aria-label="{{ __('Close') }}">
                        <span aria-hidden="true" class="text-black">&times;</span>
                    </button>
                </div>
            </div>
            <div class="modal-body text-white text-center overflow-auto pb-0">
                @include('shared.forms.select', [
                   'name' => 'printer_id',
                   'containerClass' => 'float-right w-100',
                   'label' => '',
                   'error' => false,
                   'value' => app('printer')->getDefaultLabelPrinter($order->customer)->id ?? '',
                   'options' => ['pdf' => __('Generate PDF')] + $printers->pluck('hostnameAndName', 'id')->toArray()
                ])
            </div>
            <div class="modal-footer mx-auto">
                <button type="button" class="confirm-button btn bg-logoOrange px-5 font-weight-700 change-tab text-white" data-default-text="{{ __('Save') }}" data-dismiss="modal">
                    {{ __('Save') }}
                </button>
            </div>
        </div>
    </div>
</div>
