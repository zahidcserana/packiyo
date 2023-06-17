<?php

namespace App\Http\Controllers;

use App\Http\Requests\WebshipperCredential\DestroyRequest;
use App\Http\Requests\WebshipperCredential\StoreRequest;
use App\Http\Requests\WebshipperCredential\UpdateRequest;
use App\Models\Customer;
use Illuminate\Contracts\Foundation\Application;
use Illuminate\Contracts\View\Factory;
use Illuminate\Contracts\View\View;
use App\Models\WebshipperCredential;
use LaravelJsonApi\Laravel\Http\Controllers\Actions\FetchOne;

class WebshipperCredentialController extends Controller
{
    use FetchOne;

    public function __construct()
    {
        $this->authorizeResource(WebshipperCredential::class);
    }

    /**
     * @param Customer $customer
     * @return Application|Factory|View|\Illuminate\Foundation\Application|\Illuminate\View\View
     */
    public function index(Customer $customer)
    {
        return view('customers.webshipper_credentials', [
            'customer' => $customer->load(['webshipperCredentials'])
        ]);
    }

    /**
     * @param Customer $customer
     * @return Application|Factory|View|\Illuminate\Foundation\Application|\Illuminate\View\View
     */
    public function create(Customer $customer)
    {
        return view('webshipper_credentials.create', compact('customer'));
    }

    /**
     * @param StoreRequest $request
     * @param Customer $customer
     * @return mixed
     */
    public function store(StoreRequest $request, Customer $customer)
    {
        app('webshipperCredential')->store($request);

        return redirect()->route('customers.webshipper_credentials.index', compact('customer'))->withStatus(__('Webshipper credentials successfully created.'));
    }

    /**
     * @param Customer $customer
     * @param WebshipperCredential $webshipperCredential
     * @return Application|Factory|View|\Illuminate\Foundation\Application|\Illuminate\View\View
     */
    public function edit(Customer $customer, WebshipperCredential $webshipperCredential)
    {
        return view('webshipper_credentials.edit', compact('customer', 'webshipperCredential'));
    }

    /**
     * @param UpdateRequest $request
     * @param Customer $customer
     * @param WebshipperCredential $webshipperCredential
     * @return mixed
     */
    public function update(UpdateRequest $request, Customer $customer, WebshipperCredential $webshipperCredential)
    {
        app('webshipperCredential')->update($request, $webshipperCredential);

        return redirect()->route('customers.webshipper_credentials.index', compact('customer'))->withStatus(__('Webshipper credentials successfully updated.'));
    }

    /**
     * @param DestroyRequest $request
     * @param Customer $customer
     * @param WebshipperCredential $webshipperCredential
     * @return mixed
     */
    public function destroy(DestroyRequest $request, Customer $customer, WebshipperCredential $webshipperCredential)
    {
        app('webshipperCredential')->destroy($request, $webshipperCredential);

        return redirect()->route('customers.easypost_credentials.index', compact('customer'))->withStatus(__('Credential successfully deleted.'));
    }
}
