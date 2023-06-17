<?php

namespace App\Policies;

use App\Models\User;
use App\Models\PurchaseOrder;
use App\Models\Location;
use Illuminate\Auth\Access\HandlesAuthorization;

class PurchaseOrderPolicy
{
    use HandlesAuthorization;

    /**
     * Determine whether the user can view the purchase order.
     *
     * @param User $user
     * @param PurchaseOrder $purchaseOrder
     * @return mixed
     */
    public function view(User $user, PurchaseOrder $purchaseOrder)
    {
        return $user->isAdmin() || $purchaseOrder->customer->hasUser($user->id);
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
     * Determine whether the user can create purchase orders.
     *
     * @param User $user
     * @param  $data
     * @return mixed
     */
    public function create(User $user, $data = null)
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

    public function batchStore(User $user)
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
     * Determine whether the user can update the purchase order.
     *
     * @param User $user
     * @param null $data
     * @return mixed
     */
    public function update(User $user, $data = null)
    {
        if ($user->isAdmin()) {
            return true;
        }

        $data = $data ?: app('request')->input();

        if (isset($data['id']) && $purchaseOrder = PurchaseOrder::find($data['id'])) {
            if (!$user->hasCustomer($purchaseOrder->customer_id)) {
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
     * Determine whether the user can delete the purchase order.
     *
     * @param User $user
     * @param null $data
     * @return mixed
     */
    public function delete(User $user, $data = null)
    {
        if ($user->isAdmin()) {
            return true;
        }

        $data = $data ?: app('request')->input();

        if (isset($data['id']) && $purchaseOrder = PurchaseOrder::find($data['id'])) {
            return $user->hasCustomer($purchaseOrder->customer_id);
        }

        return true;
    }

    public function batchDelete(User $user)
    {
        $dataArr = app('request')->input();

        foreach ($dataArr as $data) {
            if (!$this->delete($user, $data)) {
                return false;
            }
        }

        return true;
    }

    public function receive(User $user, PurchaseOrder $purchaseOrder, $data = null)
    {
        if ($user->isAdmin()) {
            return true;
        }

        if ($user->hasCustomer($purchaseOrder->customer_id) == false) {
            return false;
        }

        if (isset($data['location_id']) && $location = Location::find($data['location_id'])) {
            if ($user->hasCustomer($location->warehouse->customer_id) == false) {
                return false;
            }
        }

        return true;
    }

    public function close(User $user, PurchaseOrder $purchaseOrder, $data = null): bool
    {
        if ($user->isAdmin()) {
            return true;
        }

        return $user->hasCustomer($purchaseOrder->customer_id) !== false;
    }

    public function reject(User $user, PurchaseOrder $purchaseOrder, $data = null): bool
    {
        if ($user->isAdmin()) {
            return true;
        }

        return $user->hasCustomer($purchaseOrder->customer_id) !== false;
    }

    public function batchReceive(User $user, PurchaseOrder $purchaseOrder)
    {
        $dataArr = app('request')->input();

        foreach ($dataArr as $key => $data) {
            if ($this->receive($user, $purchaseOrder, $data) == false) {
                return false;
            }
        }

        return true;
    }

    public function history(User $user, PurchaseOrder $purchaseOrder)
    {
        if ($user->isAdmin()) {
            return true;
        }

        return $user->hasCustomer($purchaseOrder->customer_id) != false;
    }

    public function itemHistory(User $user, PurchaseOrder $purchaseOrder)
    {
        if ($user->isAdmin()) {
            return true;
        }

        return $user->hasCustomer($purchaseOrder->customer_id) != false;
    }
}
