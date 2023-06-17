<div class="modal-header">
    <button type="button" class="close" data-dismiss="modal" aria-label="{{ __('Close') }}">
        <span aria-hidden="true" class="text-black">&times;</span>
    </button>
</div>
<form action="" method="post" style="display: inline-block" id="closePurchaseOrderForm">
    {!! csrf_field() !!}
<div class="text-center"><h2 class="text-logoOrange">{{ __('Close Purchase Order') }}</h2></div>
<div class="modal-body text-black text-center py-3">
    Are you sure you want to close this purchase order?
</div>
<div class="modal-footer">
    <button type="submit" class="btn bg-logoOrange mx-auto px-5 text-white closePurchaseOrder">{{ __('Confirm') }}</button>
</div>
</form>
