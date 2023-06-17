<?php

namespace App\Policies;

use App\Models\User;
use App\Models\Warehouse;
use Illuminate\Auth\Access\HandlesAuthorization;

class WarehousePolicy
{
    use HandlesAuthorization;

    /**
     * Determine whether the user can view the warehouse.
     *
     * @param User $user
     * @param Warehouse $warehouse
     * @return mixed
     */
    public function view(User $user, Warehouse $warehouse)
    {
        return $user->isAdmin() || $warehouse->customer->hasUser($user->id);
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
     * Determine whether the user can create warehouses.
     *
     * @param User $user
     * @param  $data
     * @return mixed
     */
    public function create(User $user, $data = null): mixed
    {
        if ($user->isAdmin()) {
            return true;
        }

        $data = $data ?: app('request')->input();

        if (isset($data['customer_id'])) {
            return $user->hasCustomer($data['customer_id']);
        }

        return true;
    }

    public function batchStore(User $user): bool
    {
        $dataArr = app('request')->input();

        foreach ($dataArr as $data) {
            if (!$this->create($user, $data)) {
                return false;
            }
        }

        return true;
    }

    /**
     * Determine whether the user can update the warehouse.
     *
     * @param User $user
     * @param  $data
     * @return mixed
     */
    public function update(User $user, $data = null): mixed
    {
        if ($user->isAdmin()) {
            return true;
        }

        $data = $data ?: app('request')->input();

        if (isset($data['id']) && $warehouse = Warehouse::find($data['id'])) {
            if (!$user->hasCustomer($warehouse->customer_id)) {
                return false;
            }
        }

        if (isset($data['customer_id'])) {
            return $user->hasCustomer($data['customer_id']);
        }

        return true;
    }

    public function batchUpdate(User $user): bool
    {
        $dataArr = app('request')->input();

        foreach ($dataArr as $data) {
            if (!$this->update($user, $data)) {
                return false;
            }
        }

        return true;
    }

    /**
     * Determine whether the user can delete the warehouse.
     *
     * @param User $user
     * @param  $data
     * @return mixed
     */
    public function delete(User $user, $data = null): mixed
    {
        if ($user->isAdmin()) {
            return true;
        }

        $data = $data ?: app('request')->input();

        if (isset($data['id']) && $warehouse = Warehouse::find($data['id'])) {
            return $user->hasCustomer($warehouse->customer_id);
        }

        return true;
    }

    public function batchDelete(User $user): bool
    {
        $dataArr = app('request')->input();

        foreach ($dataArr as $data) {
            if (!$this->delete($user, $data)) {
                return false;
            }
        }

        return true;
    }
}
