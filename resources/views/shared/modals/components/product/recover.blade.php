<div class="modal fade confirm-dialog" id="recover-product-modal" role="dialog">
    <div class="modal-dialog modal-md modal-dialog-centered" role="document">
        <div class="modal-content bg-white">
            <form method="post" autocomplete="off" class="recover-product-form modal-content">
                @csrf
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal" aria-label="{{ __('Close') }}">
                        <span aria-hidden="true" class="text-black">&times;</span>
                    </button>
                </div>
                <div class="text-center"><h2 class="text-logoOrange">{{ __('Recover product') }}</h2></div>
                <div class="modal-body text-black text-center py-3">
                    Are you sure you want to recover this product?
                </div>
                <div class="modal-footer">
                    <button type="submit" class="btn bg-logoOrange mx-auto px-5 text-white recover-product">{{ __('Confirm') }}</button>
                </div>
            </form>
        </div>
    </div>
</div>
