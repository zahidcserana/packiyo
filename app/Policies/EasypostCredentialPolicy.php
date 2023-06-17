<?php

namespace App\Policies;

use App\Models\User;
use App\Models\EasypostCredential;
use Illuminate\Auth\Access\HandlesAuthorization;

class EasypostCredentialPolicy
{
    use HandlesAuthorization;

    /**
     * Determine whether the user can view the easypost credential.
     *
     * @param User $user
     * @param EasypostCredential $easypostCredential
     * @return bool
     */
    public function view(User $user, EasypostCredential $easypostCredential)
    {
        return $user->isAdmin() || $easypostCredential->customer->hasUser($user->id);
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
     * Determine whether the user can create easypost credential.
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
     * Determine whether the user can update the easypost credential.
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

        if (isset($data['id']) && $easypostCredential = EasypostCredential::find($data['id'])) {
            if ($user->hasCustomer($easypostCredential->customer_id) == false) {
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

        foreach ($dataArr as $data) {
            if (!$this->update($user, $data)) {
                return false;
            }
        }

        return true;
    }

    /**
     * Determine whether the user can delete the easypost credential.
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

        if (isset($data['id']) && $easypostCredential = EasypostCredential::find($data['id'])) {
            return $user->hasCustomer($easypostCredential->customer_id);
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
}
