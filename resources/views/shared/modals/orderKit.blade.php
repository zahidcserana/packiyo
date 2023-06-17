<div class="modal fade confirm-dialog" id="cancelKitItem" tabindex="-1" role="dialog">
    <div class="modal-dialog modal-dialog-centered" role="document">
        <div class="modal-content p-3" id="cancelKitItem">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="{{ __('Close') }}">
                    <span aria-hidden="true" class="text-black">&times;</span>
                </button>
            </div>
            <div class="modal-body text-black py-3 overflow-auto">
                Are you sure you want to cancel the following item ?
            </div>
            <div class="modal-footer">
                <div class="justify-content-around d-flex w-100">
                    <button type="submit" class="btn mx-auto px-5 text-black" data-dismiss="modal" >Cancel</button>
                    <button type="button" class="btn bg-logoOrange mx-auto px-5 text-white cancelled-order-item" data-product-id="">Ok</button>
                </div>
            </div>
        </div>
    </div>
</div>
