<?php

namespace App\Policies;

use Illuminate\Auth\Access\Response;
use App\Models\GovOrg;
use App\Models\User;

class GovOrgPolicy
{
    /**
     * Determine whether the user can view any models.
     */
    public function viewAny(User $user): bool
    {
        return $user->checkPermissionTo('view-any GovOrg');
    }

    /**
     * Determine whether the user can view the model.
     */
    public function view(User $user, GovOrg $govorg): bool
    {
        return $user->checkPermissionTo('view GovOrg');
    }

    /**
     * Determine whether the user can create models.
     */
    public function create(User $user): bool
    {
        return $user->checkPermissionTo('create GovOrg');
    }

    /**
     * Determine whether the user can update the model.
     */
    public function update(User $user, GovOrg $govorg): bool
    {
        return $user->checkPermissionTo('update GovOrg');
    }

    /**
     * Determine whether the user can delete the model.
     */
    public function delete(User $user, GovOrg $govorg): bool
    {
        return $user->checkPermissionTo('delete GovOrg');
    }

    /**
     * Determine whether the user can delete any models.
     */
    public function deleteAny(User $user): bool
    {
        return $user->checkPermissionTo('delete-any GovOrg');
    }

    /**
     * Determine whether the user can restore the model.
     */
    public function restore(User $user, GovOrg $govorg): bool
    {
        return $user->checkPermissionTo('restore GovOrg');
    }

    /**
     * Determine whether the user can restore any models.
     */
    public function restoreAny(User $user): bool
    {
        return $user->checkPermissionTo('restore-any GovOrg');
    }

    /**
     * Determine whether the user can replicate the model.
     */
    public function replicate(User $user, GovOrg $govorg): bool
    {
        return $user->checkPermissionTo('replicate GovOrg');
    }

    /**
     * Determine whether the user can reorder the models.
     */
    public function reorder(User $user): bool
    {
        return $user->checkPermissionTo('reorder GovOrg');
    }

    /**
     * Determine whether the user can permanently delete the model.
     */
    public function forceDelete(User $user, GovOrg $govorg): bool
    {
        return $user->checkPermissionTo('force-delete GovOrg');
    }

    /**
     * Determine whether the user can permanently delete any models.
     */
    public function forceDeleteAny(User $user): bool
    {
        return $user->checkPermissionTo('force-delete-any GovOrg');
    }
}
