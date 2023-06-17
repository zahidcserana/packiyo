<?php

namespace App\Policies;

use App\Models\User;
use App\Models\Tag;
use Illuminate\Auth\Access\HandlesAuthorization;

class TagPolicy
{
    use HandlesAuthorization;

    /**
     * Determine whether the user can view the tag.
     *
     * @param  \App\Models\User  $user
     * @param  \App\Models\Tag  $tag
     * @return mixed
     */
    public function view(User $user, Tag $tag)
    {
        return $user->isAdmin() || $tag->customer->hasUser($user->id);
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
     * Determine whether the user can create tags.
     *
     * @param  \App\Models\User  $user
     * @param  $data
     * @return mixed
     */
    public function create(User $user, Tag $tag)
    {
        return $user->isAdmin() || $tag->customer->hasUser($user->id);
    }

    public function batchStore(User $user, Tag $tag)
    {
        return $user->isAdmin() || $tag->customer->hasUser($user->id);
    }

    /**
     * Determine whether the user can update the tag.
     *
     * @param  \App\Models\User  $user
     * @param  $data
     * @return mixed
     */
    public function update(User $user, Tag $tag)
    {
        return $user->isAdmin() || $tag->customer->hasUser($user->id);
    }

    public function batchUpdate(User $user, Tag $tag)
    {
        return $user->isAdmin() || $tag->customer->hasUser($user->id);
    }

    /**
     * Determine whether the user can delete the tag.
     *
     * @param  \App\Models\User  $user
     * @param  $data
     * @return mixed
     */
    public function delete(User $user, Tag $tag)
    {
        return $user->isAdmin() || $tag->customer->hasUser($user->id);
    }

    public function batchDelete(User $user, Tag $tag)
    {
        return $user->isAdmin() || $tag->customer->hasUser($user->id);
    }

}
