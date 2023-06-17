<form method="post" action="{{ route('profile.password') }}" autocomplete="off"
      enctype="multipart/form-data">
    @csrf
    @method('put')

    <x-toastr key="password_status" />

    <div class="form-group mb-0 mx-2 text-left mb-3">
        <label for=""
               class="text-neutral-text-gray font-weight-600 font-xs"
               data-id="old_password">{{ __('Current password') }} </label>
        <div
            class="input-group input-group-alternative input-group-merge bg-lightGrey font-sm tableSearch mw-100">
            <input
                class="form-control font-sm bg-lightGrey font-weight-600 text-neutral-gray h-auto p-2"
                placeholder=""
                type="password"
                name="old_password">
        </div>
    </div>
    <div class="d-lg-flex justify-content-md-between">
        <div class="flex-grow-1">
            <div class="form-group mb-0 mx-2 text-left mb-3">
                <label for=""
                       class="text-neutral-text-gray font-weight-600 font-xs"
                       data-id="password">{{ __('New password') }}</label>
                <div
                    class="input-group input-group-alternative input-group-merge bg-lightGrey font-sm tableSearch mw-100">
                    <input
                        class="form-control font-sm bg-lightGrey font-weight-600 text-neutral-gray h-auto p-2"
                        placeholder=""
                        type="password"
                        name="password">
                </div>
            </div>
        </div>
        <div class="flex-grow-1">
            <div class="form-group mb-0 mx-2 text-left mb-3">
                <label for=""
                       class="text-neutral-text-gray font-weight-600 font-xs"
                       data-id="password">{{ __('Confirm New Password') }}</label>
                <div
                    class="input-group input-group-alternative input-group-merge bg-lightGrey font-sm tableSearch mw-100">
                    <input
                        class="form-control font-sm bg-lightGrey font-weight-600 text-neutral-gray h-auto p-2"
                        placeholder=""
                        type="password"
                        name="password_confirmation">
                </div>
            </div>
        </div>
    </div>
    <button type="submit" class="btn bg-logoOrange mx-auto px-5 font-weight-700 mt-5 mb-2 change-tab text-white d-block" data-id="">
        {{ __('Change Password') }}
    </button>
</form>
