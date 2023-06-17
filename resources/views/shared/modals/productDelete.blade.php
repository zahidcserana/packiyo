<div class="modal fade confirm-dialog" id="productDeleteModal" tabindex="-1" role="dialog">
    <div class="modal-dialog modal-sm modal-dialog-centered" role="document">
        <div class="modal-content productForm">
            <div class="modal-header px-0 d-flex flex-column align-items-center">
                <div class="pb-0 mr-4 d-flex w-100 justify-content-end">
                    <button type="button" class="close px-0 pt-2 pb-0 mx-2" data-dismiss="modal" aria-label="{{ __('Close') }}">
                        <span aria-hidden="true" class="text-black">&times;</span>
                    </button>
                </div>
                <i class="picon-trash-filled icon-2xl icon-gray" title="Delete"></i>
            </div>
            <div class="modal-body text-white text-center pb-3 pt-0 overflow-auto">
                @csrf
                <h2 class="text-logo-orange font-lg font-weight-600">{{ __('Delete Product') }}</h2>
                <p class="font-sm text-center font-weight-600 text-black">{{ __('Are you sure you want to delete this product?') }}</p>
                <p class="deleteItemName font-sm text-center font-weight-600 text-black"></p>
                <button data-id="0" class="btn bg-logoOrange mx-auto px-5 font-weight-700 change-tab text-white deleteProductButton border-12">{{ __('OK') }}</button>
            </div>
        </div>
    </div>
</div>
