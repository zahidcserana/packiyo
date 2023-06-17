<div class="container-fluid userContainer">
    @if(is_null($customer))
        <h4>{{ __('Please select a customer in the top right') }}</h4>
    @else
        <div class="border-bottom py-2 d-flex align-items-center">
            <h6 class="modal-title text-black text-left">
                {{ __('Payment method') }}
            </h6>
        </div>
        @if($customer->hasPaymentMethod())
            <div class="d-flex row mt-3">
                <div class="col-12">
                    <p><span class="text-neutral-text-gray font-weight-600">{{ __('Type') }}</span>: <span class="font-weight-600 text-black">{{ $customer->pm_type }}</span></p>
                </div>
                <div class="col-12">
                    <p><span class="text-neutral-text-gray font-weight-600">{{ __('Last four digits') }}</span>: <span class="font-weight-600 text-black">{{ $customer->pm_last_four }}</span></p>
                </div>
            </div>
            <div class="d-flex justify-content-center">
                <a
                    href="{{ route('account.payment-method', compact('customer')) }}"
                    class="btn bg-logoOrange text-white mx-auto px-5 font-weight-700 mt-5">
                    {{ __('Edit') }}
                </a>
            </div>
        @else
            <div class="d-flex justify-content-center">
                <a
                    href="{{ route('account.payment-method', compact('customer')) }}"
                    class="btn bg-logoOrange text-white mx-auto px-5 font-weight-700 mt-5">
                    {{ __('Add payment method') }}
                </a>
            </div>
        @endif
        <div class="border-bottom py-2 d-flex align-items-center">
            <h6 class="modal-title text-black text-left">
                {{ __('Billing details') }}
            </h6>
        </div>
        <div class="d-flex row mt-3">
            <div class="col-12">
                <p><span class="text-neutral-text-gray font-weight-600">{{ __('Name') }}</span>: <span class="font-weight-600 text-black">{{ $customer->billingDetails->account_holder_name ?? ''}}</span></p>
            </div>
            <div class="col-12">
                <p><span class="text-neutral-text-gray font-weight-600">{{ __('Address') }}</span>: <span class="font-weight-600 text-black">{{ $customer->billingDetails->address ?? '' }}</span></p>
            </div>
            <div class="col-12">
                <p><span class="text-neutral-text-gray font-weight-600">{{ __('City') }}</span>: <span class="font-weight-600 text-black">{{ $customer->billingDetails->city ?? '' }}</span></p>
            </div>
            <div class="col-12">
                <p><span class="text-neutral-text-gray font-weight-600">{{ __('Country') }}</span>: <span class="font-weight-600 text-black">{{ isset($customer->billingDetails->country_id) ? \Webpatser\Countries\Countries::find($customer->billingDetails->country_id)->name : '' }}</span></p>
            </div>
            <div class="col-12">
                <p><span class="text-neutral-text-gray font-weight-600">{{ __('Email address') }}</span>: <span class="font-weight-600 text-black">{{ $customer->billingDetails->email ?? '' }}</span></p>
            </div>
        </div>
        <div class="d-flex justify-content-center">
            <a
                href="{{ route('account.billing-details', compact('customer')) }}"
                class="btn bg-logoOrange text-white mx-auto px-5 font-weight-700 mt-5">
                {{ __('Edit') }}
            </a>
        </div>
    </div>
@endif
</div>
