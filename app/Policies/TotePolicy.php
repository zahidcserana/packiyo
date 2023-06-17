<?php

namespace App\Policies;

use App\Models\Tote;
use App\Models\User;
use Illuminate\Auth\Access\HandlesAuthorization;

class TotePolicy
{
    use HandlesAuthorization;

    /**
     * Determine whether the user can view the tote.
     *
     * @param User $user
     * @param Tote $tote
     * @return bool
     */
    public function view(User $user, Tote $tote): bool
    {
        return $user->isAdmin() || $tote->warehouse->customer->hasUser($user->id);
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
     * Determine whether the user can create orders.
     *
     * @param User $user
     * @param $data
     * @return bool
     */
    public function create(User $user, $data = null): bool
    {
        return true;
    }

    public function batchStore(User $user): bool
    {
        return true;
    }

    /**
     * Determine whether the user can update the order.
     *
     * @param User $user
     * @param  $data
     * @return bool
     */
    public function update(User $user, $data = null): bool
    {
        return true;
    }

    public function batchUpdate(User $user): bool
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

        if (isset($data['id']) && $tote = Tote::find($data['id'])) {
            return $user->hasCustomer($tote->warehouse->customer_id);
        }

        return true;
    }

    public function batchDelete(User $user): bool
    {
        $dataArr = app('request')->input();

        foreach ($dataArr as $key => $data) {
            if (!$this->delete($user, $data)) {
                return false;
            }
        }

        return true;
    }
}
