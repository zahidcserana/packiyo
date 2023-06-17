<?php

namespace App\Policies;

use App\Models\User;
use App\Models\Webhook;
use Illuminate\Auth\Access\HandlesAuthorization;

class WebhookPolicy
{
    use HandlesAuthorization;

    /**
     * Determine whether the user can view the webhook.
     *
     * @param  \App\Models\User  $user
     * @param  \App\Models\Webhook $webhook
     * @return mixed
     */
    public function view(User $user, Webhook $webhook)
    {
        if ($user->isAdmin()) {
            return true;
        } else {
            return $user->hasCustomer($webhook->customer_id);
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

    /**
     * Determine whether the user can create webhooks.
     *
     * @param  \App\Models\User  $user
     * @param $data
     * @return mixed
     */
    public function create(User $user, $data = null)
    {
        if ($user->isAdmin()) {
            return true;
        }

        $data = $data ? $data : app('request')->input();

        if (isset($data['customer_id'])) {
            if ($user->hasCustomer($data['customer_id']) == false) {
                return false;
            }
        }

        if (isset($data['user_id'])) {
            $userIds = app()->user->getAllCustomerUserIds($user);

            return in_array($data['user_id'], $userIds );
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
     * Determine whether the user can update the webhook.
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

        if (isset($data['id']) && $webhook = Webhook::find($data['id'])) {
            if ($user->hasCustomer($webhook->customer_id) == false) {
                return false;
            }
        }

        if (isset($data['customer_id'])) {
            if ($user->hasCustomer($data['customer_id']) == false ) {
                return false;
            }
        }

        if (isset($data['user_id'])) {
            $userIds = app()->user->getAllCustomerUserIds($user);

            return in_array($data['user_id'], $userIds );
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
     * Determine whether the user can delete the webhook.
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

        $data = $data ? $data : app('request')->input();

        if (isset($data['id']) && $webhook = Webhook::find($data['id'])) {
            return $user->hasCustomer($webhook->customer_id);
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
