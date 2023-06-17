<div class="modal fade confirm-dialog" id="cancel-subscription">
    <div class="modal-dialog modal-dialog-centered" role="document">
        <div class="modal-content">
            <form method="post" action="{{ route('account.cancel-subscription', ['customer' => $customer]) }}" autocomplete="off">
                @csrf
                <div class="modal-header px-0 d-flex flex-column align-items-center">
                    <h2 class="text-black font-lg font-weight-600 modal-title">
                        {{ __('Cancellation') }}
                    </h2>
                </div>
                <div class="text-center pb-3">
                    <span class="text-neutral-text-gray">{{ __('Please write down the reason for your cancellation: ') }}</span>
                    <input type="text" class="border font-sm font-weight-600 text-neutral-gray h-auto p-2 rounded" id="cancellation-reason">
                </div>
                <div class="modal-body overflow-auto py-0">
                    <div class="">
                        <div class="font-weight-600 font-md">{{ __('Cancel Packiyo account') }}</div>
                        <div class="text-neutral-text-gray">
                            {{ __('When you cancel Packiyo account, you\'ll have only 5 additional days to access your data, customer information, and settings after the end of your billing cycle. Your subdomain will also be deactivated then. Learn about the') }}  <a href="#" class="text-black text-underline"><em>{{ __('Packiyo Deletion Policy.') }}</em></a>{{ __(' For questions, contact ') }}<a href="#" class="text-black text-underline"><em>{{ __('Customer Support') }}</em></a>.
                        </div>
                    </div>
                </div>
                <div class="modal-footer mx-auto">
                    <button type="button" class="btn btn-secondary modal-button-cancel" data-dismiss="modal">
                        {{ __('Cancel') }}
                    </button>
                    <button type="submit" class="btn btn-secondary modal-button-cancel" id="remove-subscription">
                        {{ __('Remove Subscription') }}
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

