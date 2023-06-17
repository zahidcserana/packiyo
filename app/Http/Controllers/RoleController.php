<?php

namespace App\Http\Controllers;

use App\Models\{UserRole, User};
use App\Http\Requests\UserRoleRequest;
use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Contracts\{Foundation\Application, View\Factory, View\View};
use Illuminate\Http\RedirectResponse;

class RoleController extends Controller
{
    public function __construct()
    {
        $this->authorizeResource(UserRole::class);
    }

    /**
     * Display a listing of the roles
     *
     * @param UserRole $model
     * @return Application|Factory|View
     * @throws AuthorizationException
     */
    public function index(UserRole $model)
    {
        $this->authorize('manage-users', User::class);

        return view('roles.index', ['roles' => $model->all()]);
    }

    /**
     * Show the form for creating a new role
     *
     * @return Application|Factory|View
     */
    public function create()
    {
        return view('roles.create');
    }

    /**
     * Store a newly created role in storage
     *
     * @param UserRoleRequest $request
     * @param UserRole $model
     * @return RedirectResponse
     */
    public function store(UserRoleRequest $request, UserRole $model)
    {
        $model->create($request->all());

        return redirect()->route('role.index')->withStatus(__('Role successfully created.'));
    }

    /**
     * Show the form for editing the specified role
     *
     * @param UserRole $role
     * @return Application|Factory|View
     */
    public function edit(UserRole $role)
    {
        return view('roles.edit', compact('role'));
    }

    /**
     * Update the specified role in storage
     *
     * @param UserRoleRequest $request
     * @param UserRole $role
     * @return RedirectResponse
     */
    public function update(UserRoleRequest $request, UserRole $role)
    {
        $role->update($request->all());

        return redirect()->route('role.index')->withStatus(__('Role successfully updated.'));
    }
}
