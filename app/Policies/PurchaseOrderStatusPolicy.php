<?php

namespace App\Policies;

use App\Models\User;
use App\Models\PurchaseOrderStatus;
use Illuminate\Auth\Access\HandlesAuthorization;

class PurchaseOrderStatusPolicy
{
    use HandlesAuthorization;

    /**
     * Determine whether the user can view the purchase order status.
     *
     * @param  \App\Models\User  $user
     * @param  \App\Models\PurchaseOrderStatus  $purchaseOrderStatus
     * @return mixed
     */
    public function view(User $user, PurchaseOrderStatus $purchaseOrderStatus)
    {
        return $user->isAdmin() || $purchaseOrderStatus->customer->hasUser($user->id);
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
     * Determine whether the user can create purchase order statuses.
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
     * Determine whether the user can update the purchase order status.
     *
     * @param  \App\Models\User  $user
     * @return mixed
     */
    public function update(User $user, $data = null)
    {
        if ($user->isAdmin()) {
            return true;
        }

        $data = $data ? $data : app('request')->input();

        if (isset($data['id']) && $purchaseOrderStatus = PurchaseOrderStatus::find($data['id'])) {
            if ($user->hasCustomer($purchaseOrderStatus->customer_id) == false) {
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
     * Determine whether the user can delete the purchase order status.
     *
     * @param  \App\Models\User  $user
     * @return mixed
     */
    public function delete(User $user, $data = null)
    {
        if ($user->isAdmin()) {
            return true;
        }

        $data = $data ? $data : app('request')->input();

        if (isset($data['id']) && $purchaseOrderStatus = PurchaseOrderStatus::find($data['id'])) {
            return $user->hasCustomer($purchaseOrderStatus->customer_id);
        }

        return true;
    }

    public function batchDelete(User $user)
    {
        $dataArr = app('request')->input();

        foreach ($dataArr as $key => $data) {
            if ($this->delete( $user, $data) == false) {
                return false;
            }
        }

        return true;
    }
}
