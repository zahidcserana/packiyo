<div class="modal fade successReset bg-lightGreyTransparent" id="successReset" tabindex="-1" role="dialog">
    <div class="modal-dialog" style="top: 50%" role="document">
        <div class="modal-content">
            @if(session('success'))
                <div class="modal-header flex-column align-items-center">
                    <img src="{{ asset('img/success.svg') }}" alt="">
                </div>
                <div class="modal-body text-white text-center p-0">
                    <h6 class="text-center text-logo-orange font-text-lg text-center" id="modal-title-notification">{{ __('Success!') }}</h6>
                    <p class="text-center text-black font-weight-600">{{ __('Your Password has been changed') }}</p>
                </div>
                <div class="modal-footer formSubmit">
                    <button type="button" class="btn bg-logoOrange text-white mx-auto px-5 font-weight-700" data-dismiss="modal">{{ __('Ok') }}</button>
                </div>
            @else
                <div class="modal-header flex-column align-items-center">
                    <img src="{{ asset('img/error.svg') }}" alt="">
                </div>
                <div class="modal-body text-white text-center p-0">
                    <h6 class="text-center text-logo-orange font-text-lg text-center" id="modal-title-notification">{{ __('Unsuccess!') }}</h6>
                    <p class="text-center text-black font-weight-600">{{ __('Your Password has not been changed') }}</p>
                </div>
                <div class="modal-footer formSubmit">
                    <a href="{{ route('password.request') }}" class="btn bg-logoOrange text-white mx-auto px-5 font-weight-700" data-dismiss="modal">{{ __('Try Again') }}</a>
                </div>
            @endif
        </div>
    </div>
</div>

