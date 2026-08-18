<?php

namespace App\Policies;

use App\Models\Tags;
use App\Models\User;

class TagsPolicy
{
    /**
     * Determine whether the user can view any models.
     *
     * Tags have no per-user ownership; any authenticated user may access all tags.
     */
    public function viewAny(User $user): bool
    {
        return true;
    }

    /**
     * Determine whether the user can view the model.
     */
    public function view(User $user, Tags $tags): bool
    {
        return true;
    }

    /**
     * Determine whether the user can create models.
     */
    public function create(User $user): bool
    {
        return true;
    }

    /**
     * Determine whether the user can update the model.
     */
    public function update(User $user, Tags $tags): bool
    {
        return true;
    }

    /**
     * Determine whether the user can delete the model.
     */
    public function delete(User $user, Tags $tags): bool
    {
        return true;
    }
}
