<?php

namespace App\Policies;

use App\Models\User;

/**
 * Roles and permissions are admin-only.
 *
 * This used to be done by copying the package's RoleResource into
 * app/Filament/Resources under the package namespace, which declared the same
 * class twice and broke `php artisan route:cache`.
 */
class RolePolicy
{
    public function viewAny(User $user): bool
    {
        return $user->hasRole('admin');
    }

    public function view(User $user): bool
    {
        return $user->hasRole('admin');
    }

    public function create(User $user): bool
    {
        return $user->hasRole('admin');
    }

    public function update(User $user): bool
    {
        return $user->hasRole('admin');
    }

    public function delete(User $user): bool
    {
        return $user->hasRole('admin');
    }

    public function deleteAny(User $user): bool
    {
        return $user->hasRole('admin');
    }
}
