<div class="modal fade confirm-dialog" id="pack-item-serial-number-input-modal" role="dialog">
    <div class="modal-dialog modal-dialog-centered" role="document">
        <div class="modal-content productForm">
            <input type="hidden" name="customer_id" value="{{ $order->customer->id }}" class="customer_id" />
            <div class="modal-header px-0">
                <div class="mx-4 pb-4 d-flex w-100 border-bottom-gray">
                    <h6 class="modal-title text-black text-left"
                        id="modal-title-notification">{{ __('Add serial number') }}</h6>
                    <button type="button" class="close" data-dismiss="modal" aria-label="{{ __('Close') }}">
                        <span aria-hidden="true" class="text-black">&times;</span>
                    </button>
                </div>
            </div>
            <form method="POST" action="{{ route('packing.ship', ['order' => $order]) }}" autocomplete="off" id="serial-number-create-form" enctype="multipart/form-data">
                @csrf
                <div class="modal-body text-white pb-3 pt-0 overflow-auto">
                    @include('shared.forms.addSerialNumber', [
                        'name' => 'serial_number',
                    ])
                    <button type="button" data-dismiss="modal" class="btn bg-logoOrange text-white mx-auto px-5 font-weight-700 confirm-button mt-5 d-flex justify-content-center" id="serial-number-set-button">
                        {{ __('Save') }}
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
