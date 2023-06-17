<?php

namespace App\Policies;

use App\Models\LocationType;
use App\Models\User;
use Illuminate\Auth\Access\HandlesAuthorization;

class LocationTypesPolicy
{
    use HandlesAuthorization;

    /**
     * Determine whether the user can view the order status.
     *
     * @param User $user
     * @param LocationType $locationType
     * @return bool
     */
    public function view(User $user, LocationType $locationType): bool
    {
        return $user->isAdmin() || $locationType->customer->hasUser($user->id);
    }

    /**
     * Determine whether the user can view.
     *
     * @param  User  $user
     * @return bool
     */
    public function viewAny(User $user): bool
    {
        return true;
    }

    /**
     * Determine whether the user can create order statuses.
     *
     * @param User $user
     * @param null $data
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

    public function batchStore(User $user): bool
    {
        $dataArr = app('request')->input();

        foreach ($dataArr as $key => $data) {
            if (!$this->create($user, $data)) {
                return false;
            }
        }

        return true;
    }

    /**
     * Determine whether the user can update the order status.
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

        if (isset($data['id']) && ($locationType = LocationType::find($data['id'])) && !$user->hasCustomer($locationType->customer_id)) {
            return false;
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
     * Determine whether the user can delete the order status.
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

        if (isset($data['id']) && $locationType = LocationType::find($data['id'])) {
            return $user->hasCustomer($locationType->customer_id);
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
