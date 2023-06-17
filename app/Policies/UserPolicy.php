<?php

namespace App\Policies;

use App\Models\User;
use App\Models\CustomerUser;
use App\Models\CustomerUserRole;
use Illuminate\Auth\Access\HandlesAuthorization;

class UserPolicy
{
    use HandlesAuthorization;

    /**
     * Determine whether the user can view the model.
     *
     * @param User $user
     * @param User $model
     * @return bool
     */
    public function view(User $user, User $model): bool
    {
        if ($user->isAdmin()) {
            return true;
        }

        if ($user->id === $model->id) {
            return true;
        }

        $userCustomerIds = $user->customers()->pluck('customer_user.customer_id')->unique()->toArray();

        return CustomerUser::where('user_id', $model->id)->whereIn('customer_id', $userCustomerIds)->first();
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
     * Determine whether the user can create models.
     *
     * @param User $user
     * @param null $data
     * @return bool
     */
    public function create(User $user, $data = null): bool
    {
        if ($user->isAdmin()) {
            return true;
        }

        return app('user')->is3plCustomer();
    }

    /**
     * @param User $user
     * @return bool
     */
    public function batchStore(User $user): bool
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
     * Determine whether the user can update the model.
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

        if (isset($data['email'])) {
            if ($user->email === $data['email']) {
                return true;
            }

            $model = User::where('email', $data['email'])->first();

            $customerIds = $user->customers()->wherePivot('role_id', '=', CustomerUserRole::ROLE_CUSTOMER_ADMINISTRATOR)->pluck('customer_user.customer_id')->unique()->toArray();

            return CustomerUser::where('user_id', $model->id)->whereIn('customer_id', $customerIds)->first();
        }

        return true;
    }

    /**
     * @param User $user
     * @return bool
     */
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
     * Determine whether the user can delete the model.
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

        if (isset($data['email'])) {
            if ($user->email === $data['email']) {
                return false;
            }

            $model = User::where('email', $data['email'])->first();

            $customerIds = $user->customers()->wherePivot('role_id', '=', CustomerUserRole::ROLE_CUSTOMER_ADMINISTRATOR)->pluck('customer_user.customer_id')->unique()->toArray();

            return CustomerUser::where('user_id', $model->id)->whereIn('customer_id', $customerIds)->first();
        }

        return true;
    }

    /**
     * @param User $user
     * @return bool
     */
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

    /**
     * Determine whether the user can restore the model.
     *
     * @param User $user
     * @param User $model
     * @return bool
     */
    public function restore(User $user, User $model): bool
    {
        return true;
    }

    /**
     * Determine whether the user can permanently delete the model.
     *
     * @param User $user
     * @param User $model
     * @return bool
     */
    public function forceDelete(User $user, User $model): bool
    {
        return $user->isAdmin() && $user->id !== $model->id;
    }

    public function customers(User $user, User $model)
    {
        if ($user->isAdmin()) {
            return true;
        }

        if ($user->id === $model->id) {
            return true;
        }

        $userCustomerIds = $user->customers()->pluck('customer_user.customer_id')->unique()->toArray();

        return CustomerUser::where('user_id', $model->id)->whereIn('customer_id', $userCustomerIds)->first();
    }

    public function webhooks(User $user, User $model)
    {
        if ($user->isAdmin()) {
            return true;
        }

        if ($user->id === $model->id) {
            return true;
        }

        $userCustomerIds = $user->customers()->pluck('customer_user.customer_id')->unique()->toArray();

        return CustomerUser::where('user_id', $model->id)->whereIn('customer_id', $userCustomerIds)->first();
    }

    public function deleteAccessToken(User $user, User $model)
    {
        if ($user->isAdmin()) {
            return true;
        }

        if ($user->id === $model->id) {
            return true;
        }

        $customerIds = $user->customers()->wherePivot('role_id', '=', CustomerUserRole::ROLE_CUSTOMER_ADMINISTRATOR)->pluck('customer_user.customer_id')->unique()->toArray();

        return CustomerUser::where('user_id', $model->id)->whereIn('customer_id', $customerIds)->first();
    }
}
