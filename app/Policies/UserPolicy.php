<?php

namespace App\Policies;

use App\Models\User;
use Illuminate\Auth\Access\Response;

class UserPolicy
{
    /**
     * Determine whether the user can view any models.
     */
    public function viewAny(User $user)
    {
        return false ;
    }

    /**
     * Determine whether the user can view the model.
     */
    public function view(User $user, user $model)
    {
        if (!$user->checkPermissionTo('show user')) {
            return false;
        }
        if ($model->id == auth()->user()?->id) {
            return true;
        }
        return $user->hasRole("Super Admin");
    }
    
    /**
     * Determine whether the user can create models.
     */
    public function create(User $user)
    {
        return false ;
    }

    /**
     * Determine whether the user can update the model.
     */
    public function update(User $user, user $model)
    {
        if (!$user->checkPermissionTo('update user')) {
            return false;
        }
        if ($model->id == auth()->user()?->id) {
            return true;
        }
        return $user->hasRole("Super Admin");
    }

    /**
     * Determine whether the user can delete the model.
     */
    public function delete(User $user, user $model)
    {
        return false ;
    }

    /**
     * Determine whether the user can restore the model.
     */
    public function restore(User $user, user $model)
    {
        return false ;
    }

    /**
     * Determine whether the user can permanently delete the model.
     */
    public function forceDelete(User $user, user $model)
    {
        return false ;
    }
}
