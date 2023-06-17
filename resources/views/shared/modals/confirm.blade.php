<div class="modal fade confirm-dialog" id="confirm-modal" tabindex="-1" role="dialog">
    <div class="modal-dialog modal-dialog-centered" role="document">
        <div class="modal-content">
            <div class="modal-header px-0 d-flex flex-column align-items-center">
                <div class="pb-0 mr-4 d-flex w-100 justify-content-end">
                    <button type="button" class="close px-0 pt-2 pb-0 mx-2" data-dismiss="modal" aria-label="{{ __('Close') }}">
                        <span aria-hidden="true" class="text-black">&times;</span>
                    </button>
                </div>
                <h2 class="text-logo-orange font-lg font-weight-600 modal-title" data-default-title="{{ __('Confirmation') }}"></h2>
            </div>
            <div class="modal-body text-white text-center overflow-auto py-0">
                <span class="modal-icon font-header-lg" data-default-icon="picon-alert-circled-light"></span>
                <p class="font-md text-center font-weight-400 text-black modal-message"></p>
            </div>
            <div class="modal-footer mx-auto">
                <button type="button" class="btn btn-secondary modal-button-cancel" data-dismiss="modal">
                    {{ __('Cancel') }}
                </button>
                <button class="btn bg-logoOrange px-5 font-weight-700 change-tab text-white modal-button-confirm" data-default-confirm-button-text="{{ __('Confirm') }}" data-dismiss="modal"></button>
            </div>
        </div>
    </div>
</div>
