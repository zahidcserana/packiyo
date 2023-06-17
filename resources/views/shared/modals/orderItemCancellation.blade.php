<div class="modal fade" id="order-item-cancellation-dialog" tabindex="-1" role="dialog">
    <div class="modal-dialog modal-dialog-centered" role="document">
        <form class="modal-content bg-white" id="itemCancellationForm" method="POST">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="{{ __('Close') }}">
                    <span aria-hidden="true" class="text-black">&times;</span>
                </button>
            </div>
            <div class="text-center">
                <i class="picon-stop-light icon-lg"></i>
            </div>
            <div class="text-center"></div>
            <div class="modal-body text-black text-center py-3 custom-form-checkbox d-flex flex-column align-items-center">
                <h2 class="text-black">{{ __('Cancel Order Item') }} <span class="orderSkuNumber"></span></h2>
                <p>Are you sure you want to cancel <span class="productName"></span> from Order #<span class="orderNumber"></span>?</p>
            </div>
            <div class="modal-footer">
                <div class="justify-content-around d-flex w-100">
                    @csrf
                    <button type="submit" class="btn bg-logoOrange mx-auto px-5 text-white">{{ __('Yes') }}</button>
                    <button type="button" data-dismiss="modal" class="btn bg-logoOrange mx-auto px-5 text-white">{{ __('No') }}</button>
                </div>
            </div>
        </form>
    </div>
</div>
