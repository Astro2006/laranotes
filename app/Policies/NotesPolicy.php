<?php

namespace App\Policies;

use App\Models\Notes;
use App\Models\User;

class NotesPolicy
{
    /**
     * Determine whether the user can view any models.
     *
     * Notes have no per-user ownership; any authenticated user may access all notes.
     */
    public function viewAny(User $user): bool
    {
        return true;
    }

    /**
     * Determine whether the user can view the model.
     */
    public function view(User $user, Notes $notes): bool
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
    public function update(User $user, Notes $notes): bool
    {
        return true;
    }

    /**
     * Determine whether the user can delete the model.
     */
    public function delete(User $user, Notes $notes): bool
    {
        return true;
    }
}
