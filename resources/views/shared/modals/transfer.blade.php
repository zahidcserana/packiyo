<div class="modal fade confirm-dialog" id="confirm-dialog" tabindex="-1" role="dialog">
    <div class="modal-dialog" role="document">
        <div class="modal-content bg-gradient-danger">
            <div class="modal-header">
                <h6 class="modal-title text-white" id="modal-title-notification">{{ __('Confirmation') }}</h6>
                <button type="button" class="close" data-dismiss="modal" aria-label="{{ __('Close') }}">
                    <span aria-hidden="true" class="text-white">&times;</span>
                </button>
            </div>
            <div class="modal-body text-white text-center py-3"></div>
            <div class="modal-footer">
                <button type="button" class="btn btn-white confirm-button">{{ __('Confirm') }}</button>
                <button type="button" class="btn btn-link text-white ml-auto" data-dismiss="modal">{{ __('Close') }}</button>
            </div>
        </div>
    </div>
</div>

