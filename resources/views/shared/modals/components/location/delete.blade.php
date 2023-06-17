<div class="modal-header">
    <button type="button" class="close" data-dismiss="modal" aria-label="{{ __('Close') }}">
        <span aria-hidden="true" class="text-black">&times;</span>
    </button>
</div>
<div class="text-center">
    <i class="picon-trash-filled icon-2xl icon-gray" title="Delete"></i>
</div>
<form action="" method="post" style="display: inline-block" id="deleteLocationForm">
    <input type="hidden" name="_method" value="delete">
    <input type="hidden" name="_token" value="" id="formToken">
<div class="text-center"><h2 class="text-logoOrange">{{ __('Delete') }}</h2></div>
<div class="modal-body text-black text-center py-3">
    Are you sure you want to delete this location?
</div>
<div class="modal-footer">
    <button type="submit" class="btn bg-logoOrange mx-auto px-5 text-white deleteLocation">{{ __('Confirm') }}</button>
</div>
</form>
