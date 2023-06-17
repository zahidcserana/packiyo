<div class="modal fade" id="multipleLoginModal" tabindex="-1" role="dialog" aria-labelledby="modal-notification" style="display: none;" aria-hidden="true">
    <div class="modal-dialog modal-danger modal-dialog-centered modal-" role="document">
        <div class="modal-content bg-gradient-danger">
            <div class="modal-header">
                <h6 class="modal-title" id="modal-title-notification">{{ __('Warning') }}</h6>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">×</span>
                </button>
            </div>
            <div class="modal-body">
                <div class="py-3 text-center">
                    <i class="ni ni-bell-55 ni-3x"></i>
                    <h4 class="heading mt-4">{{ __('Multiple login detected!') }}</h4>
                    <p>{{  __('Same account is already logged in on a different device.') }}</p>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-white">{{ __('Continue') }}</button>
                <button type="button" class="btn btn-link text-white ml-auto logoutBtn">
                    <i class="ni ni-user-run"></i>{{ __('Logout') }}
                </button>
            </div>
        </div>
    </div>
</div>
