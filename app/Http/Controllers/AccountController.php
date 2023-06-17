<?php

namespace App\Http\Controllers;

use App\Models\Customer;
use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Contracts\Foundation\Application;
use Illuminate\Contracts\View\Factory;
use Illuminate\Contracts\View\View;
use Illuminate\Support\Collection;
use Symfony\Component\HttpFoundation\Response;

class AccountController extends Controller
{
    /**
     * @return Factory|View|Application
     */
    public function settings(): Factory|View|Application
    {
        $customer = app('user')->getSessionCustomer();

        if ($customer) {
            $invoices = app('payment')->invoices($customer);
            $upcomingInvoice = app('payment')->upcomingInvoice($customer);
            $subscription = $customer->subscription();
        }

        return view('account.settings',
            [
                'customer' => app('user')->getSessionCustomer(),
                'invoices' => $invoices ?? Collection::empty(),
                'upcomingInvoice' => $upcomingInvoice ?? null,
                'subscription' => $subscription ?? null
            ]);
    }

    /**
     * @param Customer $customer
     * @return Factory|View|Application
     * @throws AuthorizationException
     */
    public function paymentMethod(Customer $customer): Factory|View|Application
    {
        $this->authorize('billing', [Customer::class, $customer]);

        $intent = $customer->createSetupIntent();

        return view(
            'account.settings.payment.setPaymentMethod',
            compact('customer', 'intent')
        );
    }

    /**
     * @param Customer $customer
     * @return Factory|View|Application
     * @throws AuthorizationException
     */
    public function billingDetails(Customer $customer): Factory|View|Application
    {
        $this->authorize('billing', [Customer::class, $customer]);

        $billingDetails = $customer->billingDetails;

        return view(
            'account.settings.payment.billingDetails',
            compact('billingDetails', 'customer')
        );
    }

    /**
     * @param Customer $customer
     * @param $invoiceId
     * @return Response
     * @throws AuthorizationException
     */
    public function downloadInvoice(Customer $customer, $invoiceId): Response
    {
        $this->authorize('billing', [Customer::class, $customer]);

        return $customer->downloadInvoice($invoiceId, [
            'product' => $customer->subscription()->name ?? 'Subscription invoice'
        ]);
    }
}
