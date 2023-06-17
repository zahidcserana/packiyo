<div class="modal fade confirm-dialog" id="export-kit-items-modal" role="dialog">
    <div class="modal-dialog modal-md modal-dialog-centered" role="document">
        <div class="modal-content bg-white">
            <form method="post" action="{{ route('product.exportKitItemsCsv', $product->id) }}" autocomplete="off" id="export-kit-items-form" class="export-form modal-content">
                @csrf
                <input type="hidden" name="customer_id" value="{{ $product->customer_id }}" class="customer_id" />
                <div class="modal-header border-bottom mx-4 px-0">
                    <h6 class="modal-title text-black text-left" id="modal-title-notification">{{ __('Export kit items to CSV') }}</h6>
                    <button type="button" class="close" data-dismiss="modal" aria-label="{{ __('Close') }}">
                        <span aria-hidden="true" class="text-black">&times;</span>
                    </button>
                </div>
                <div class="modal-footer">
                    <button type="submit" class="btn bg-logoOrange mx-auto px-5 text-white export-kit-items">{{ __('Export') }}</button>
                </div>
            </form>
        </div>
    </div>
</div>
