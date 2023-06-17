<?php

namespace App\Policies;

use App\Models\User;
use App\Models\WebshipperCredential;
use Illuminate\Auth\Access\HandlesAuthorization;

class WebshipperCredentialPolicy
{
    use HandlesAuthorization;

    /**
     * Determine whether the user can view the webshipper credential.
     *
     * @param  \App\Models\User  $user
     * @param  \App\Models\WebshipperCredential $webshipperCredential
     * @return mixed
     */
    public function view(User $user, WebshipperCredential $webshipperCredential)
    {
        return $user->isAdmin() || $webshipperCredential->customer->hasUser($user->id);
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
     * Determine whether the user can create webshipper credential.
     *
     * @param  \App\Models\User  $user
     * @param  $data
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
            if ($this->create($user, $data) == false) {
                return false;
            }
        }

        return true;
    }

    /**
     * Determine whether the user can update the webshipper credential.
     *
     * @param  \App\Models\User  $user
     * @param  $data
     * @return mixed
     */
    public function update(User $user, $data = null)
    {
        if ($user->isAdmin()) {
            return true;
        }

        $data = $data ? $data : app('request')->input();

        if (isset($data['id']) && $webshipperCredential = WebshipperCredential::find($data['id'])) {
            if ($user->hasCustomer($webshipperCredential->customer_id) == false) {
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
     * Determine whether the user can delete the webshipper credential.
     *
     * @param  \App\Models\User  $user
     * @param  \App\Models\WebshipperCredential $webshipperCredential
     * @return mixed
     */
    public function delete(User $user, $data = null)
    {
        if ($user->isAdmin()) {
            return true;
        }

        $data = $data ? $data : app('request')->input();

        if (isset($data['id']) && $webshipperCredential = WebshipperCredential::find($data['id'])) {
            return $user->hasCustomer($webshipperCredential->customer_id);
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
