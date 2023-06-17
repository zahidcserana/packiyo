<?php

namespace App\Policies;

use App\Models\User;
use App\Models\InventoryLog;
use Illuminate\Auth\Access\HandlesAuthorization;

class InventoryLogPolicy
{
    use HandlesAuthorization;

    /**
     * Determine whether the user can view the inventoryLog.
     *
     * @param  \App\Models\User  $user
     * @param  \App\Models\inventoryLog $inventoryLog
     * @return mixed
     */
    public function view(User $user, InventoryLog $inventoryLog)
    {
        if ($user->isAdmin()) {
            return true;
        } else {
            $userIds = app()->user->getAllCustomerUserIds($user);

            return in_array($inventoryLog->user_id, $userIds);
        }
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
}
