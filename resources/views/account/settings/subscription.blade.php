<div class="p-4" id="subscription-tab">
    <div class="row mb-4">
        <div class="col-12 font-weight-600 font-md">
            {{ __('Current Subscription') }}
        </div>
    </div>
    <div class="row">
        @if($upcomingInvoice)
            <div class="form-control-label text-neutral-text-gray font-weight-600 font-md">
                {{ __('Your account is billed monthly and the next payment is due on') }} <a
                    href="{{ route('account.download-invoice', ['customer' => $customer, 'invoice' => $upcomingInvoice->id]) }}">{{ $upcomingInvoice->dueDate()->format('F m Y') }}</a>
            </div>
        @endif
    </div>
    <div class="row my-2 d-flex align-items-center justify-content-between">
        <div class="form-control-label text-neutral-text-gray font-weight-600 font-md">
            {{ __('Click Manage to make changes to your account. You can add billing admins under Account contracts.') }}
        </div>
        <button type="button" id="manage-subscription-button"
                class="btn bg-logoOrange borderOrange text-white mx-auto px-4 px-md-5 font-weight-700 confirm-button">
            {{ __('Manage') }}
        </button>
    </div>
    <div class="row columns">
        <button type="button" class="action-button font-sm font-weight-600">{{ __('View billing admins') }}</button>
    </div>
</div>

<div class="d-none p-4" id="manage-subscription-tab">
    <div id="manage-subscription-tab-content" class="d-flex justify-content-between">
        <div class="row mb-4">
            <div class="col-12 font-weight-600 font-md">
                {{ __('Manage subscription') }}
            </div>
        </div>
        <div class="my-2 d-flex align-items-center">
            <div class="form-control-label text-neutral-text-gray font-weight-600 font-md">
                {{ __('Your current plan:') }}
            </div>
            <div class="pl-2">
                <div><span class="text-black font-weight-600">{{ __('Free Trial') }}, </span>{{ __('expires') }} <span
                        id="subscription-expiry-date">{{ $subscription ? $subscription->ends_at->format('F d Y') : \Carbon\Carbon::now()->format('F d Y') }}</span><i
                        class="picon-upload-filled" title="Upgrade"></i></div>
            </div>
        </div>
    </div>
    <div class="tab-content text-black bg-lightGrey border-12 p-3 inputs-container d-flex" id="suite-content">
        <div class="font-weight-600 text-black font-md">
            {{ __('Packiyo Suite') }}
        </div>
        <div class="suite">
            <div class="pt-4 pl-6">
                <label for="is_kit" class="text-neutral-text-gray font-weight-600 font-sm" data-id="type">
                    {{ __('Plan') }}
                </label>
                <div class="">
                    <select
                        class="form-control font-sm bg-white font-weight-600 text-neutral-gray h-auto p-2 type-select border-0 rounded"
                        type="text"
                        name=""
                        id=""
                    >
                        <option value="1">{{ __('Free Trial') }}</option>
                    </select>
                </div>
                <div class="">
                    <div class="font-xxs text-neutral-text-gray pt-2">{{ __('14 days Free Trial') }}</div>
                    <div class="row columns">
                        <button type="button" class="action-button font-sm font-weight-600 text-underline pl-3"
                                data-toggle="modal"
                                data-target="#upgrade-enterprise">{{ __('Upgrade to Enterprise') }}</button>
                    </div>
                </div>
            </div>
        </div>
        <div class="suite">
            <div class="pt-4 pl-6">
                <label for="" class="text-neutral-text-gray font-weight-600 font-sm">{{ __('Users') }}</label>
                <div>
                    <input class="font-sm font-weight-600 text-neutral-gray h-auto p-2 border-0 rounded" type="number"
                           value="0">
                </div>
            </div>
        </div>
    </div>
    <div class="d-flex justify-content-end py-2" id="manage-subscription-footer">
        <div class="row columns">
            <button type="button" class="action-button font-sm font-weight-600 text-underline" data-toggle="modal"
                    data-target="#cancel-subscription">{{ __('Cancel Subscription') }}</button>
        </div>
        <div class="row columns pl-5">
            <button type="button"
                    class="action-button font-sm font-weight-600 text-underline">{{ __('Privacy Policy') }}</button>
        </div>
    </div>
    <div>
        <i class="picon-arrow-backward-filled" title="Back" id="back-to-current-subscription"></i>
    </div>
</div>

@if($customer)
    @include('shared.modals.components.account.cancelSubscription')
    @include('shared.modals.components.account.upgradeEnterprise')
@endif
