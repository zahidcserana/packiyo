<?php

namespace App\Policies;

use App\Models\User;
use App\Models\Order;
use App\Models\Location;
use Carbon\Carbon;
use Illuminate\Auth\Access\HandlesAuthorization;

class OrderPolicy
{
    use HandlesAuthorization;

    /**
     * Determine whether the user can view the order.
     *
     * @param User $user
     * @param  \App\Models\Order  $order
     * @return mixed
     */
    public function view(User $user, Order $order)
    {
        return $user->isAdmin() || $order->customer->hasUser($user->id);
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
     * Determine whether the user can create orders.
     *
     * @param User $user
     * @param $data
     * @return mixed
     */
    public function create(User $user, $data = null)
    {

        return true;
    }

    public function batchStore(User $user)
    {


        return true;
    }

    /**
     * Determine whether the user can update the order.
     *
     * @param User $user
     * @param  $data
     * @return mixed
     */
    public function update(User $user, $data = null)
    {

        return true;
    }

    public function batchUpdate(User $user)
    {


        return true;
    }

    /**
     * Determine whether the user can delete the order.
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

        $data = $data ?: app('request')->input();

        if (isset($data['id']) && $order = Order::find($data['id'])) {
            return $user->hasCustomer($order->customer_id);
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

    public function shipItem(User $user, Order $order, $data = null)
    {
        if ($user->isAdmin()) {
            return true;
        }

        if ($user->hasCustomer($order->customer_id) == false) {
            return false;
        }

        if (isset($data['location_id']) && $location = Location::find($data['location_id'])) {
            if ($user->hasCustomer($location->warehouse->customer_id) == false) {
                return false;
            }
        }

        return true;
    }

    public function ship(User $user, Order $order)
    {
        $dataArr = app('request')->input();

        foreach ($dataArr as $key => $data) {
            if ($this->shipItem($user, $order, $data) == false) {
                return false;
            }
        }

        return true;
    }

    public function cancel(User $user, Order $order)
    {
        return $this->update($user, $order);
    }

    public function markAsFulfilled(User $user, Order $order)
    {
        return $this->update($user, $order);
    }

    public function history(User $user, Order $order)
    {
        if ($user->isAdmin()) {
            return true;
        }

        if (!$user->hasCustomer($order->customer_id)) {
            return false;
        }

        return true;
    }

    public function itemHistory(User $user, Order $order)
    {
        if ($user->isAdmin()) {
            return true;
        }

        if (!$user->hasCustomer($order->customer_id)) {
            return false;
        }

        return true;
    }

    public function singleOrderShipping(User $user, Order $order)
    {
        return !Carbon::parse($order->required_shipping_date_at)->isFuture();

        $foundPickingBatchItems = false;
        foreach($order->orderItems as $orderItem){
            if( count($orderItem->pickingBatchItems) > 0 ){
                foreach($orderItem->pickingBatchItems as $pickingBatchItem){
                    if(count($pickingBatchItem->pickingBatch->tasks) > 0){
                        foreach($pickingBatchItem->pickingBatch->tasks as $task){
                            if( is_null($task->completed_at) ){
                                $foundPickingBatchItems = true;
                            }
                        }
                    }
                }
            }
        }

        return !$foundPickingBatchItems;
    }

    public function reship(User $user, Order $order)
    {
        if ($user->isAdmin()) {
            return true;
        }

        if ($user->hasCustomer($order->customer_id) == false) {
            return false;
        }

        return true;
    }
}
