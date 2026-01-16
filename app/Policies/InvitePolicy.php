<?php

namespace App\Policies;

use Illuminate\Auth\Access\Response;
use App\Models\Invite;
use App\Models\User;

class InvitePolicy
{
    /**
     * Determine whether the user can view any models.
     */
    public function viewAny(User $user): bool
    {
        return false;
    }

    /**
     * Determine whether the user can view the model.
     */
    public function view(User $user, Invite $invite): bool
    {
        if (!$user->checkPermissionTo('show invite')) {
            return false;
        }
        if ($invite->user_id == auth()->user()?->id) {
            return true;
        }
        return $user->hasRole("Super Admin");
    }

    /**
     * Determine whether the user can create models.
     */
    public function create(User $user): bool
    {
        return $user->checkPermissionTo('create invite');
    }

    /**
     * Determine whether the user can update the model.
     */
    public function update(User $user, Invite $invite): bool
    {
        if (!$user->checkPermissionTo('update invite')) {
            return false;
        }
        if ($invite->user_id == auth()->user()?->id) {
            return true;
        }
        return $user->hasRole("Super Admin");
    }

    /**
     * Determine whether the user can delete the model.
     */
    public function delete(User $user, Invite $invite): bool
    {
        if (!$user->checkPermissionTo('delete invite')) {
            return false;
        }
        if ($invite->user_id == auth()->user()?->id) {
            return true;
        }
        return $user->hasRole("Super Admin");
    }

    /**
     * Determine whether the user can delete any models.
     */
    public function deleteAny(User $user): bool
    {
        return false;
    }

    /**
     * Determine whether the user can restore the model.
     */
    public function restore(User $user, Invite $invite): bool
    {
        return false;
    }

    /**
     * Determine whether the user can restore any models.
     */
    public function restoreAny(User $user): bool
    {
        return false;
    }

    /**
     * Determine whether the user can replicate the model.
     */
    public function replicate(User $user, Invite $invite): bool
    {
        return false;
    }

    /**
     * Determine whether the user can reorder the models.
     */
    public function reorder(User $user): bool
    {
        return true;
    }

    /**
     * Determine whether the user can permanently delete the model.
     */
    public function forceDelete(User $user, Invite $invite): bool
    {
        return false;
    }

    /**
     * Determine whether the user can permanently delete any models.
     */
    public function forceDeleteAny(User $user): bool
    {
        return false;
    }
}
