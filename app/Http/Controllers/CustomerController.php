<?php

namespace App\Http\Controllers;

use App\Http\Requests\Customer\DestroyRequest;
use App\Http\Requests\Customer\StoreRequest;
use App\Http\Requests\Customer\UpdateRequest;
use App\Http\Requests\Customer\UpdateUsersRequest;
use App\Http\Resources\CustomerTableResource;
use App\Models\Customer;
use App\Models\CustomerUserRole;
use App\Models\Printer;
use App\Models\ShippingBox;
use App\Models\User;
use Illuminate\Contracts\Foundation\Application;
use Illuminate\Contracts\View\Factory;
use Illuminate\Contracts\View\View;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class CustomerController extends Controller
{
    public function __construct()
    {
        $this->authorizeResource(Customer::class);
        $this->middleware('3pl')->only(['store', 'create']);
    }

    public function index()
    {
        $customer = app('user')->getSelectedCustomers();

        return view('customers.index', [
            'datatableOrder' => app('editColumn')->getDatatableOrder('customers'),
            'customer' => $customer
        ]);
    }

    /**
     * @return Application|Factory|View
     */
    public function create()
    {
        $customers = app('user')->getSelectedCustomers();
        $parentCustomer = null;

        foreach ($customers as $customer) {
            if ($customer->parent_id) {
                $parentCustomer = $customer;
            }
        }

        return view('customers.create', compact('customers', 'parentCustomer'));
    }

    public function dataTable(Request $request): JsonResponse
    {
        $tableColumns = $request->get('columns');
        $columnOrder = $request->get('order');
        $sortColumnName = 'customers.id';
        $sortDirection = 'desc';

        if (!empty($columnOrder)) {
            $sortColumnName = $tableColumns[$columnOrder[0]['column']]['name'];
            $sortDirection = $columnOrder[0]['dir'];
        }

        $customerCollection = Customer::join('contact_informations', 'customers.id', '=', 'contact_informations.object_id')
            ->where('contact_informations.object_type', Customer::class)
            ->select('customers.*')
            ->groupBy('customers.id')
            ->orderBy($sortColumnName, $sortDirection);

        $customers = app('user')->getSelectedCustomers()->pluck('id')->toArray();

        $customerCollection = $customerCollection->whereIn('customers.id', $customers);

        $term = $request->get('search')['value'];

        if ($term) {
            // TODO: sanitize term
            $term = $term . '%';

            $customerCollection
                ->whereHas('contactInformation', function($query) use ($term) {
                    $query->where('name', 'like', $term)
                        ->orWhere('company_name', 'like', $term)
                        ->orWhere('address', 'like', $term)
                        ->orWhere('address2', 'like', $term)
                        ->orWhere('zip', 'like', $term)
                        ->orWhere('city', 'like', $term)
                        ->orWhere('email', 'like', $term)
                        ->orWhere('phone', 'like', $term);
                });
        }

        $orders = $customerCollection->skip($request->get('start'))->limit($request->get('length'))->get();

        return response()->json([
            'data' => CustomerTableResource::collection($orders),
            'visibleFields' => app('editColumn')->getVisibleFields('customers'),
            'recordsTotal' => PHP_INT_MAX,
            'recordsFiltered' => PHP_INT_MAX,
        ]);

    }

    /**
     * @param StoreRequest $request
     * @return JsonResponse
     */
    public function store(StoreRequest $request): JsonResponse
    {
        app('customer')->store($request);

        return response()->json([
            'success' => true,
            'message' => __('Customer successfully created.')
        ]);
    }

    /**
     * @param Request $request
     * @param Customer $customer
     * @return Application|Factory|View
     */
    public function edit(Request $request, Customer $customer)
    {
        $customerUsersIds = $customer->users()->pluck('users.id')->toArray();
        $allUsers = User::all();
        $routeName = $request->route()->getName();
        $allRoles = CustomerUserRole::all();

        $settings = customer_settings($customer->id);

        $printers = Printer::where('customer_id', $customer->id)->pluck('name', 'id');
        $shippingBoxes = ShippingBox::where('customer_id', $customer->id)->pluck('name', 'id');

        $data = [
            'customer' => $customer,
            'allUsers' => $allUsers,
            'customerUsersIds' => $customerUsersIds,
            'roles' => $allRoles,
            'settings' => $settings,
            'printers' => $printers,
            'shippingBoxes' => $shippingBoxes,
            'lotPriorities' => config('settings.lot_priorities')
        ];

        return match ($routeName) {
            'customer.editUsers' => view('customers.editUsers', $data),
            'customer.cssOverrides' => view('customers.cssOverrides', $data),
            default => view('customers.editCustomer', $data)
        };
    }

    /**
     * @param UpdateRequest $request
     * @param Customer $customer
     * @return JsonResponse
     */
    public function update(UpdateRequest $request, Customer $customer): JsonResponse
    {
        app('customer')->update($request, $customer);

        return response()->json([
            'success' => true,
            'message' => __('Customer successfully updated.')
        ]);
    }

    public function destroy(DestroyRequest $request, Customer $customer)
    {
        app('customer')->destroy($request, $customer);

        return redirect()->route('customer.index')->withStatus(__('Customer successfully deleted.'));
    }

    public function updateGeneralSettings(Customer $customer, Request $request)
    {
        return response()->json([
            'success' => true,
            'message' => __('Settings successfully updated.')
        ]);
    }

    public function detachUser(Customer $customer, User $user)
    {
        $this->authorize('updateUsers', $customer);

        app('customer')->detachUser($customer, $user);

        return redirect()->back()->withStatus(__('Customer successfully updated.'));
    }

    public function updateUsers(UpdateUsersRequest $request, Customer $customer)
    {
        $this->authorize('updateUsers', $customer);

        app('customer')->updateUsers($request, $customer);

        return redirect()->back()->withStatus(__('Customer successfully updated.'));
    }

    public function filterUsers(Request $request, Customer $customer)
    {
        return app('customer')->filterUsers($request, $customer);
    }

    public function getDimensionUnits(Request $request, Customer $customer)
    {
        return app('customer')->getDimensionUnits($customer);
    }
}
