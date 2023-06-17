<?php

namespace App\Policies;

use App\Models\ShippingMethod;
use App\Models\User;
use Illuminate\Auth\Access\HandlesAuthorization;

class ShippingMethodPolicy
{
    use HandlesAuthorization;

    /**
     * Determine whether the user can view any models.
     */
    public function viewAny(): bool
    {
        return true;
    }

    /**
     * Determine whether the user can view the model.
     */
    public function view(User $user, ShippingMethod $shippingMethod): bool
    {
        return $user->isAdmin() || $shippingMethod->customer->hasUser($user);
    }

    /**
     * Determine whether the user can update the model.
     */
    public function update(User $user, $data = null): bool
    {
        if ($user->isAdmin()) {
            return true;
        }

        $data = $data ?: app('request')->input();

        if (isset($data['id']) && $shippingMethod = ShippingMethod::find($data['id'])) {
            if (! $user->hasCustomer($shippingMethod->customer_id)) {
                return false;
            }
        }

        if (isset($data['customer_id'])) {
            return $user->hasCustomer($data['customer_id']);
        }

        return true;
    }

    /**
     * Determine whether the user can batch update the models.
     */
    public function batchUpdate(User $user): bool
    {
        $dataArr = app('request')->input();

        foreach ($dataArr as $data) {
            if (! $this->update($user, $data)) {
                return false;
            }
        }

        return true;
    }
}
