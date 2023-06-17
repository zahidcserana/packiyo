<?php

namespace App\Policies;

use App\Models\User;
use App\Models\Return_;
use App\Models\Order;
use App\Models\Location;
use Illuminate\Auth\Access\HandlesAuthorization;

class ReturnsPolicy
{
    use HandlesAuthorization;

    /**
     * Determine whether the user can view the returns.
     *
     * @param User $user
     * @param  \App\Models\Return_  $returns
     * @return mixed
     */
    public function view(User $user, Return_ $return)
    {
        return $user->isAdmin() || $return->order->customer->hasUser($user->id);
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
     * Determine whether the user can create returns.
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

        $data = $data ? $data : app('request')->input();

        if (isset($data['order_id']) && $order = Order::find($data['order_id'])) {
            return $user->hasCustomer($order->customer->id);
        }

        return true;
    }

    public function batchStore(User $user)
    {
        $dataArr = app('request')->input();

        foreach ($dataArr as $key => $data) {
            if ($this->create($user, $data) == false) {
                return false;
            }
        }

        return true;
    }

    /**
     * Determine whether the user can update the returns.
     *
     * @param User $user
     * @param  $data
     * @return mixed
     */
    public function update(User $user, $data = null)
    {
        if ($user->isAdmin()) {
            return true;
        }

        $data = $data ?: app('request')->input();

        if (isset($data['number']) && $return = Return_::where('number', $data['number'])->first()) {
            return $user->hasCustomer($return->order->customer_id);
        }

        return true;
    }

    public function batchUpdate(User $user)
    {
        $dataArr = app('request')->input();

        foreach ($dataArr as $key => $data) {
            if ($this->update( $user, $data) == false) {
                return false;
            }
        }

        return true;
    }

    /**
     * Determine whether the user can delete the returns.
     *
     * @param User $user
     * @param  $data
     * @return mixed
     */
    public function delete(User $user, $data = null)
    {
        if ($user->isAdmin()) {
            return true;
        }

        $data = $data ? $data : app('request')->input();

        if (isset($data['id']) && $return = Return_::find($data['id'])) {
            return $user->hasCustomer($return->order->customer_id);
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

    public function receive(User $user, Return_ $return, $data = null)
    {
        if ($user->isAdmin()) {
            return true;
        };

        if ($user->hasCustomer($return->order->customer_id) == false) {
            return false;
        }

        if (isset($data['location_id']) && $location = Location::find($data['location_id'])) {
            if ($user->hasCustomer($location->warehouse->customer_id) == false) {
                return false;
            }
        }

        return true;
    }

    public function batchReceive(User $user, Return_ $return)
    {
        $dataArr = app('request')->input();

        foreach ($dataArr as $key => $data) {
            if ($this->receive($user, $return, $data) == false) {
                return false;
            }
        }

        return true;
    }

    public function history(User $user, Return_ $return)
    {
        if ($user->isAdmin()) {
            return true;
        };

        if ($user->hasCustomer($return->order->customer_id) == false) {
            return false;
        }

        return true;
    }

    public function itemHistory(User $user, Return_ $return)
    {
        if ($user->isAdmin()) {
            return true;
        };

        if ($user->hasCustomer($return->order->customer_id) == false) {
            return false;
        }

        return true;
    }
}
