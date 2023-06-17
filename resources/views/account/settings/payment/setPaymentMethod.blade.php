@extends('layouts.app')

@section('content')
    @component('layouts.headers.auth', [
        'title' => __('Account'),
        'subtitle' => __('Add payment method')
    ])
    @endcomponent
    <div class="container-fluid ">
        <form action="{{ route('payment.storeMethod', compact('customer')) }}" method="post"
              class="card px-3 py-4 border-8" id="store-payment-method">
            @csrf
            <div class="row">
                <div class="col-4">
                    <div class="form-group">
                        <label class="form-control-label text-neutral-text-gray font-weight-600 font-xs" for="card-element">
                            {{ __('Card details') }}
                        </label>
                        <div id="payment-element" class="my-2"></div>
                    </div>
                </div>
            </div>
            <div class="row">
                <div class="col-4 ml-lg-8">
                    <a href="{{ url()->previous() }}" type="button" class="btn bg-logoOrange mx-auto px-5 font-weight-700 mt-5 change-tab text-white">{{ __('Cancel') }}</a>
                    <button type="button" class="btn bg-logoOrange mx-auto px-5 font-weight-700 mt-5 change-tab text-white" id="card-button" data-secret="{{ $intent->client_secret }}">{{ __('Save') }}</button>
                </div>
            </div>
        </form>
    </div>
@endsection
@push('js')
    <script src="https://js.stripe.com/v3/"></script>
    <script>
        const appearance = {
            theme: 'stripe'
        }
        const stripe = Stripe("{{ config('stripe.key') }}")

        const cardButton = document.getElementById('card-button')
        const clientSecret = cardButton.dataset.secret

        const elements = stripe.elements({clientSecret, appearance})
        let cardElement = elements.create('payment')

        cardElement.mount('#payment-element')

        cardButton.addEventListener('click', async (e) => {
            const { setupIntent, error } = await stripe.confirmSetup(
                {
                    elements,
                    confirmParams: {
                        return_url: '{{ route('payment.storeMethod', compact('customer')) }}',
                    },
                    redirect: 'if_required'
                }
            );

            toastr.info('Processing...')

            if (setupIntent) {
                let formData = new FormData(document.getElementById('store-payment-method'))

                formData.append('payment_method', setupIntent.payment_method)

                $.ajax({
                    type: 'POST',
                    url: '{{ route('payment.storeMethod', compact('customer')) }}',
                    headers: {'X-CSRF-TOKEN': formData.get('_token')},
                    data: formData,
                    processData: false,
                    contentType: false,
                    success: function (data) {
                        toastr.clear()
                        if (data.success === true) {
                            toastr.success(data.message)

                            window.setTimeout(function(){
                                window.location.href = '{{ route('account.settings') }}'
                            }, 2000)
                        } else {
                            toastr.error(data.message)
                        }
                    },
                    error: function (data) {
                        toastr.error(data.message)
                    }
                })
            } else {
                toastr.clear()
                toastr.error(error.message)
            }
        })
    </script>
@endpush
