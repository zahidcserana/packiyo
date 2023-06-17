<?php

namespace App\Components;

use App\Http\Requests\Payment\StorePaymentMethodRequest;
use App\Http\Requests\Payment\UpdateBillingDetailsRequest;
use App\Models\BillingDetails;
use App\Models\Customer;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Collection;
use Laravel\Cashier\Invoice;
use Stripe\Exception\ApiErrorException;
use Stripe\Stripe;
use Webpatser\Countries\Countries;

class PaymentComponent extends BaseComponent
{
    /**
     * Store payment method
     *
     * @param Customer $customer
     * @param StorePaymentMethodRequest $request
     * @return JsonResponse
     */
    public function storePaymentMethod(Customer $customer, StorePaymentMethodRequest $request): JsonResponse
    {
        try {
            $input = $request->validated();

            if (!$customer->hasStripeId()) {
                $customer->createAsStripeCustomer();

                $customer->updateStripeCustomer([
                    'name' => $customer->contactInformation->name,
                    'email' => $customer->contactInformation->email
                ]);

                $stripeCustomer = $customer->asStripeCustomer();

                $stripeCustomer->save();
            }

            $customer->updateDefaultPaymentMethod($input['payment_method']);

            $customer->save();

            return response()->json([
                'success' => true,
                'message' => __('Payment method added successfully.')
            ]);
        } catch (\Exception $exception) {
            return response()->json([
                'success' => false,
                'message' => $exception->getMessage()
            ]);
        }
    }

    /**
     * Update billing details
     *
     * @param Customer $customer
     * @param UpdateBillingDetailsRequest $request
     * @return mixed
     * @throws ApiErrorException
     */
    public function updateBillingDetails(Customer $customer, UpdateBillingDetailsRequest $request)
    {
        $input = $request->validated();

        if ($customer->hasStripeId()) {
            $stripeCustomer = $customer->updateStripeCustomer([
                'name' => $input['account_holder_name'],
                'email' => $input['email'],
                'phone' => $input['phone'],
                'address' => [
                    'line1' => $input['address'],
                    'line2' => $input['address2'],
                    'postal_code' => $input['postal_code'],
                    'state' => $input['state'],
                    'city' => $input['city'],
                    'country' => Countries::find($input['country_id'])->iso_3166_2
                ]
            ]);

            $stripeCustomer->save();

            return BillingDetails::updateOrCreate(['customer_id' => $customer->id], $input);
        }

        return null;
    }

    /**
     * @param Customer $customer
     * @return Collection|Invoice[]
     */
    public function invoices(Customer $customer)
    {
        if ($customer->hasStripeId()) {
            return $customer->invoices();
        }

        return Collection::empty();
    }

    /**
     * @param Customer $customer
     * @return mixed|null
     */
    public function upcomingInvoice(Customer $customer)
    {
        if ($customer->hasStripeId()) {
            return $customer->invoicesIncludingPending([
                'status' => 'open'
            ])->first();
        }

        return null;
    }

    /**
     * @param Customer $customer
     * @return bool
     */
    public function cancelSubscription(Customer $customer): bool
    {
        if ($customer->hasStripeId() && $customer->subscribed()) {
            $customer->subscription()->cancel();

            return true;
        }

        return false;
    }
}
