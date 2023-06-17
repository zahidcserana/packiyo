<?php

namespace App\Http\Controllers;

use App\Components\Shipping\Providers\EasypostShippingProvider;
use App\Http\Requests\EasypostCredential\DestroyRequest;
use App\Http\Requests\EasypostCredential\StoreRequest;
use App\Http\Requests\EasypostCredential\UpdateRequest;
use App\Models\Customer;
use App\Models\EasypostCredential;
use App\Models\Shipment;
use App\Models\ShippingCarrier;
use Illuminate\Contracts\Foundation\Application;
use Illuminate\Contracts\View\Factory;
use Illuminate\Contracts\View\View;
use LaravelJsonApi\Laravel\Http\Controllers\Actions\FetchOne;

class EasypostCredentialController extends Controller
{
    use FetchOne;

    public function __construct()
    {
        $this->authorizeResource(EasypostCredential::class);
    }

    /**
     * @param Customer $customer
     * @return Application|Factory|View|\Illuminate\Foundation\Application|\Illuminate\View\View
     */
    public function index(Customer $customer)
    {
        return view('customers.easypost_credentials', [
            'customer' => $customer->load(['easypostCredentials'])
        ]);
    }

    /**
     * @param Customer $customer
     * @return Application|Factory|View|\Illuminate\Foundation\Application|\Illuminate\View\View
     */
    public function create(Customer $customer)
    {
        return view('easypost_credentials.create', compact('customer'));
    }

    /**
     * @param StoreRequest $request
     * @param Customer $customer
     * @return mixed
     */
    public function store(StoreRequest $request, Customer $customer)
    {
        app('easypostCredential')->store($request);

        return redirect()->route('customers.easypost_credentials.index', compact('customer'))->withStatus(__('Easypost credentials successfully created.'));
    }

    /**
     * @param Customer $customer
     * @param EasypostCredential $easypostCredential
     * @return Application|Factory|View|\Illuminate\Foundation\Application|\Illuminate\View\View
     */
    public function edit(Customer $customer, EasypostCredential $easypostCredential)
    {
        return view('easypost_credentials.edit', compact('customer', 'easypostCredential'));
    }

    public function update(UpdateRequest $request, Customer $customer, EasypostCredential $easypostCredential)
    {
        app('easypostCredential')->update($request, $easypostCredential);

        return redirect()->route('customers.easypost_credentials.index', compact('customer'))->withStatus(__('Easypost credentials successfully updated.'));
    }

    /**
     * @param DestroyRequest $request
     * @param Customer $customer
     * @param EasypostCredential $easypostCredential
     * @return mixed
     */
    public function destroy(DestroyRequest $request, Customer $customer, EasypostCredential $easypostCredential)
    {
        app('easypostCredential')->destroy($request, $easypostCredential);

        return redirect()->route('customers.easypost_credentials.index', compact('customer'))->withStatus(__('Credential successfully deleted.'));
    }

    /**
     * @param Customer $customer
     * @param EasypostCredential $easypostCredential
     * @return Application|Factory|View|\Illuminate\Foundation\Application|\Illuminate\View\View
     */
    public function batches(Customer $customer, EasypostCredential $easypostCredential)
    {
        $this->authorize('view', $easypostCredential);

        $carrierBatches = [];

        $easypostCredential->load('shippingCarriers.shippingMethods');

        foreach ($easypostCredential->shippingCarriers as $shippingCarrier) {
            $carrierBatch = [
                'carrier' => $shippingCarrier,
                'batches' => []
            ];

            $shipments = Shipment::whereIn('shipping_method_id', $shippingCarrier->shippingMethods->pluck('id'))
                ->whereNotNull('external_manifest_id')
                ->where('external_manifest_id', '!=', 'ignore')
                ->whereDate('created_at', '>', now()->subDays(request('days', 2))->toDateString())
                ->groupBy('external_manifest_id')
                ->orderBy('created_at', 'desc')
                ->get();

            if (count($shipments) > 0) {
                foreach ($shipments as $shipment) {
                    $carrierBatch['batches'][] = app('easypostShipping')->getBatch($easypostCredential, $shipment->external_manifest_id);
                }

                $carrierBatches[] = $carrierBatch;
            }
        }

        return view('easypost_credentials.batches', compact('customer', 'easypostCredential', 'carrierBatches'));
    }

    public function batchShipments(Customer $customer, EasypostCredential $easypostCredential)
    {
        $this->authorize('view', $easypostCredential);

        if (app('easypostCredential')->batchShipments($easypostCredential)) {
            $status = __('Shipments batched.');
        } else {
            $status = __('Please try again');
        }

        return redirect()->back()->withStatus($status);
    }

    public function scanformBatches(Customer $customer, EasypostCredential $easypostCredential)
    {
        $this->authorize('view', $easypostCredential);

        if (app('easypostCredential')->scanformBatches($easypostCredential)) {
            $status = __('Scanforms generated.');
        } else {
            $status = __('Please try again');
        }

        return redirect()->back()->withStatus($status);
    }
}
