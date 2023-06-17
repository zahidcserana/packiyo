<?php

namespace App\Policies;

use App\Models\User;
use App\Models\Location;
use App\Models\Warehouse;
use Illuminate\Auth\Access\HandlesAuthorization;

class LocationPolicy
{
    use HandlesAuthorization;
    /**
     * Determine whether the user can view the location.
     *
     * @param  \App\Models\User  $user
     * @param  \App\Models\Location  $location
     * @return mixed
     */
    public function view(User $user, Location $location)
    {
        return $user->isAdmin() || $location->warehouse->customer->hasUser($user->id);
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
     * Determine whether the user can create locations.
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

        if (isset($data['warehouse_id']) && $warehouse = Warehouse::find($data['warehouse_id'])) {
            return $user->hasCustomer($warehouse->customer_id);
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
     * Determine whether the user can update the location.
     *
     * @param  \App\Models\User  $user
     * @param  \App\Models\Location  $location
     * @return mixed
     */
    public function update(User $user, $data = null)
    {
        if ($user->isAdmin()) {
            return true;
        }

        $data = $data ? $data : app('request')->input();

        if (isset($data['id']) && $location = Location::find($data['id'])) {
            if ($user->hasCustomer($location->warehouse->customer_id) == false) {
                return false;
            }
        }

        if (isset($data['warehouse_id']) && $warehouse = Warehouse::find($data['warehouse_id'])) {
            return $user->hasCustomer($warehouse->customer_id);
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
     * Determine whether the user can delete the location.
     *
     * @param  \App\Models\User  $user
     * @param  \App\Models\Location  $location
     * @return mixed
     */
    public function delete(User $user, $data = null)
    {
        $location = Location::find($data['id']);

        if( $location->protected ){
            return false;
        }

        if ($user->isAdmin()) {
            return true;
        }

        $data = $data ? $data : app('request')->input();

        if (isset($data['id']) && $location) {
            return $user->hasCustomer($location->warehouse->customer_id);
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
