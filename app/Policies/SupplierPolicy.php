<?php

namespace App\Policies;

use App\Models\User;
use App\Models\Supplier;
use Illuminate\Auth\Access\HandlesAuthorization;

class SupplierPolicy
{
    use HandlesAuthorization;

    /**
     * Determine whether the user can view the supplier.
     *
     * @param  \App\Models\User  $user
     * @param  \App\Models\Supplier  $supplier
     * @return mixed
     */
    public function view(User $user, Supplier $supplier)
    {
        return $user->isAdmin() || $supplier->customer->hasUser($user->id);
    }

    /**
     * Determine whether the user can view.
     *
     * @param  User  $user
     * @return bool
     */
    public function viewAny(User $user)
    {
        return true;
    }
    /**
     * Determine whether the user can create suppliers.
     *
     * @param  \App\Models\User  $user
     * @return mixed
     */
    public function create(User $user, $data = null)
    {
        if ($user->isAdmin()) {
            return true;
        }

        $data = $data ? $data : app('request')->input();

        if (isset($data['customer_id'])) {
            return $user->hasCustomer($data['customer_id']);
        }

        return true;
    }

    public function batchStore(User $user)
    {
        $dataArr = app('request')->input();

        foreach ($dataArr as $key => $data) {
            if ($this->create( $user, $data) == false) {
                return false;
            }
        }

        return true;
    }

    /**
     * Determine whether the user can update the supplier.
     *
     * @param  \App\Models\User  $user
     * @param  \App\Models\Supplier  $supplier
     * @return mixed
     */
    public function update(User $user, $data = null)
    {
        if ($user->isAdmin()) {
            return true;
        }

        $data = $data ? $data : app('request')->input();

        if (isset($data['id']) && $supplier = Supplier::find($data['id'])) {
            if ($user->hasCustomer($supplier->customer_id) == false) {
                return false;
            }
        }

        if (isset($data['customer_id'])) {
            return $user->hasCustomer($data['customer_id']);
        }

        return true;
    }

    public function batchUpdate(User $user)
    {
        $dataArr = app('request')->input();

        foreach ($dataArr as $key => $data) {
            if ($this->update($user, $data) == false) {
                return false;
            }
        }

        return true;
    }

    /**
     * Determine whether the user can delete the supplier.
     *
     * @param  \App\Models\User  $user
     * @param  \App\Models\Supplier  $supplier
     * @return mixed
     */
    public function delete(User $user, $data = null)
    {
        if ($user->isAdmin()) {
            return true;
        }

        $data = $data ? $data : app('request')->input();

        if (isset($data['id']) && $supplier = Supplier::find($data['id'])) {
            return $user->hasCustomer($supplier->customer_id);
        }

        return true;
    }

    public function batchDelete(User $user)
    {
        $dataArr = app('request')->input();

        foreach ($dataArr as $key => $data) {
            if ($this->delete($user, $data) == false) {
                return false;
            }
        }

        return true;
    }
}
