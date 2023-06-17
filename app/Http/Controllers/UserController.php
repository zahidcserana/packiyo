<?php

namespace App\Http\Controllers;

use App\Http\Requests\User\StoreRequest;
use App\Http\Requests\User\UpdateRequest;
use App\Http\Resources\UserTableResource;
use App\Models\Customer;
use App\Models\User;
use App\Models\UserRole;
use Carbon\Carbon;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Session;

class UserController extends Controller
{
    public function __construct()
    {
        $this->authorizeResource(User::class);
    }

    public function index()
    {
        return view('users.index', [
            'datatableOrder' => app()->editColumn->getDatatableOrder('users'),
        ]);
    }

    public function dataTable(Request $request): JsonResponse
    {
        $customer = app()->user->getSelectedCustomers();
        $customers = $customer->pluck('id')->toArray();

        $tableColumns = $request->get('columns');
        $columnOrder = $request->get('order');
        $sortColumnName = 'users.id';
        $sortDirection = 'desc';

        if (!empty($columnOrder)) {
            $sortColumnName = $tableColumns[$columnOrder[0]['column']]['name'];
            $sortDirection = $columnOrder[0]['dir'];
        }

        $usersCollection = User::join('contact_informations', 'users.id', '=', 'contact_informations.object_id')
            ->join('user_roles', 'users.user_role_id', '=', 'user_roles.id')
            ->join('customer_user', 'customer_user.user_id', '=', 'users.id')
            ->join('customers', 'customer_user.customer_id', '=', 'customers.id')
            ->whereIn('customers.id', $customers)
            ->where('contact_informations.object_type', User::class)
            ->select('users.*')
            ->groupBy('users.id')
            ->orderBy($sortColumnName, $sortDirection);

        $term = $request->get('search')['value'];

        if ($term) {
            // TODO: sanitize term
            $term = $term . '%';

            $usersCollection->where(function ($q) use ($term) {
                $q->where('users.email', 'like', $term)
                    ->orWhereHas('contactInformation', function ($query) use ($term) {
                        $query->where('name', 'like', $term);
                    })
                    ->orWhereHas('role', function ($query) use ($term) {
                        $query->where('name', 'like', $term);
                    });
            });
        }
        if ($request->get('length') && ((int) $request->get('length')) !== -1) {
            $usersCollection = $usersCollection->skip($request->get('start'))->limit($request->get('length'));
        }

        $users = $usersCollection->get();

        $visibleFields = app('editColumn')->getVisibleFields('users');

        return response()->json([
            'data' => UserTableResource::collection($users),
            'visibleFields' => $visibleFields,
            'recordsTotal' => PHP_INT_MAX,
            'recordsFiltered' => PHP_INT_MAX,
        ]);
    }

    public function create(User $user)
    {
        return view('users.create', [
            'user' => $user,
            'roles' => UserRole::all(),
        ]);
    }

    public function store(StoreRequest $request): JsonResponse
    {
        app()->user->store($request);

        Session::flash('status', __('User successfully created.'));

        return response()->json([
            'success' => true,
            'redirect_url' => route('settings.manageUsers')
        ]);
    }

    public function edit(User $user)
    {
        return view('users.edit', [
            'user' => $user,
            'roles' => UserRole::all(),
        ]);
    }

    public function update(UpdateRequest $request, User $user): JsonResponse
    {
        app()->user->update($request, $user);

        return response()->json([
            'success' => true,
            'message' => __('User successfully updated.'),
        ]);
    }

    public function destroy(User $user): RedirectResponse
    {
        $user->delete();

        return redirect()->back()->withStatus('User successfully deleted.');
    }

    public function setSessionCustomer(Customer $customer): RedirectResponse
    {
        app()->user->setSessionCustomer($customer);

        return redirect()->back();
    }

    public function removeSessionCustomer(): RedirectResponse
    {
        app()->user->removeSessionCustomer();

        return redirect()->back();
    }

    public function getCustomers(Request $request)
    {
        return app()->user->filterCustomers($request);
    }
}
