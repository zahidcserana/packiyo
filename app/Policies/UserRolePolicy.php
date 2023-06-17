<?php

namespace App\Policies;

use App\Models\User;
use App\Models\UserRole;
use Illuminate\Auth\Access\HandlesAuthorization;

class UserRolePolicy
{
    use HandlesAuthorization;

    /**
     * Determine whether the user can create roles.
     *
     * @param  \App\Models\User  $user
     * @param  $data
     * @return boolean
     */
    public function create(User $user, $data = null)
    {
        if ($user->isAdmin()) {
            return true;
        }

        return false;
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
     * Determine whether the user can update the role.
     *
     * @param  \App\Models\User  $user
     * @param  $data
     * @return boolean
     */
    public function update(User $user, $data = null)
    {
        if ($user->isAdmin()) {
            return true;
        }

        return false;
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
     * Determine whether the user can delete the role.
     *
     * @param  \App\Models\User  $user
     * @param  $data
     * @return mixed
     */
    public function delete(User $user, $data = null)
    {
        if ($user->isAdmin()) {
            return true;
        } 

        return false;
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