<?php

namespace App\Policies;

use App\Models\JobPosition;
use App\Models\User;
use Illuminate\Auth\Access\Response;

class JobPositionPolicy
{
    /**
     * Determine whether the user can view any models.
     */
    public function viewAny(User $user): bool
    {
        return $user->can('view_any_job::position');
    }

    /**
     * Determine whether the user can view the model.
     */
    public function view(User $user, JobPosition $jobPosition): bool
    {
        return $user->can('view_job::position');
    }

    /**
     * Determine whether the user can create models.
     */
    public function create(User $user): bool
    {
        return $user->can('create_job::position');
    }

    /**
     * Determine whether the user can update the model.
     */
    public function update(User $user, JobPosition $JobPosition): bool
    {
        return $user->can('update_job::position');
    }

    /**
     * Determine whether the user can delete the model.
     */
    public function delete(User $user, JobPosition $jobPosition): bool
    {
        return $user->can('delete_job::position');
    }

    public function deleteAny(User $user): bool
    {
        return $user->can('delete_any_job::position');
    }

    /**
     * Determine whether the user can restore the model.
     */
    public function restore(User $user, JobPosition $JobPosition): bool
    {
        return false;
    }

    /**
     * Determine whether the user can permanently delete the model.
     */
    public function forceDelete(User $user, JobPosition $JobPosition): bool
    {
        return false;
    }
}
