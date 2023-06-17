<?php

namespace App\Http\Controllers;

use App\Http\Requests\Payment\StorePaymentMethodRequest;
use App\Http\Requests\Payment\UpdateBillingDetailsRequest;
use App\Models\Customer;
use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;

class PaymentController extends Controller
{
    /**
     * Store payment method
     *
     * @param Customer $customer
     * @param StorePaymentMethodRequest $request
     * @return JsonResponse
     * @throws AuthorizationException
     */
    public function storePaymentMethod(Customer $customer, StorePaymentMethodRequest $request): JsonResponse
    {
        $this->authorize('billing', [Customer::class, $customer]);

        return app('payment')->storePaymentMethod($customer, $request);
    }

    /**
     * Update billing details
     *
     * @param Customer $customer
     * @param UpdateBillingDetailsRequest $request
     * @return mixed
     * @throws AuthorizationException
     */
    public function updateBillingDetails(Customer $customer, UpdateBillingDetailsRequest $request)
    {
        $this->authorize('billing', [Customer::class, $customer]);

        app('payment')->updateBillingDetails($customer, $request);

        return redirect()->route('account.settings')->withStatus('Billing details updated successfully.');
    }

    /**
     * Upgrade to enterprise
     *
     * @param Customer $customer
     * @return RedirectResponse
     * @throws AuthorizationException
     */
    public function upgrade(Customer $customer): RedirectResponse
    {
        $this->authorize('billing', [Customer::class, $customer]);

        // @TODO decide which screen to redirect to for upgrade

        return redirect()->route('account.settings');
    }

    /**
     * Cancel current subscription
     *
     * @param Customer $customer
     * @return mixed
     * @throws AuthorizationException
     */
    public function cancelSubscription(Customer $customer)
    {
        $this->authorize('billing', [Customer::class, $customer]);

        if (app('payment')->cancelSubscription($customer)) {
            return redirect()->back()->withStatus('You\'ve successfully unsubscribed!');
        }

        return redirect()->back()->withErrors('Customer is already unsubscribed');
    }
}
