<?php

namespace App\Policies;

use App\Models\OfficeLocation;
use App\Models\User;
use Illuminate\Auth\Access\Response;

class OfficeLocationPolicy
{
    /**
     * Determine whether the user can view any models.
     */
    public function viewAny(User $user): bool
    {
        return $user->can('view_any_office::location');
    }

    /**
     * Determine whether the user can view the model.
     */
    public function view(User $user, OfficeLocation $officeLocation): bool
    {
        return $user->can('view_office::location');
    }

    /**
     * Determine whether the user can create models.
     */
    public function create(User $user): bool
    {
        return $user->can('create_office::location');
    }

    /**
     * Determine whether the user can update the model.
     */
    public function update(User $user, OfficeLocation $officeLocation): bool
    {
        return $user->can('update_office::location');
    }

    /**
     * Determine whether the user can delete the model.
     */
    public function delete(User $user, OfficeLocation $officeLocation): bool
    {
        return $user->can('delete_office::location');
    }

    public function deleteAny(User $user): bool
    {
        return $user->can('delete_any_office::location');
    }

    /**
     * Determine whether the user can restore the model.
     */
    public function restore(User $user, OfficeLocation $officeLocation): bool
    {
        return false;
    }

    /**
     * Determine whether the user can permanently delete the model.
     */
    public function forceDelete(User $user, OfficeLocation $officeLocation): bool
    {
        return false;
    }
}
