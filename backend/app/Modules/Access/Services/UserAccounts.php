<?php

namespace App\Modules\Access\Services;

use App\Modules\Access\Models\Role;
use App\Modules\Access\Models\User;

/**
 * Public entry point for other modules that need a login account (e.g. Couriers).
 * Such accounts never get admin panel access; their permissions come from the given role.
 */
class UserAccounts
{
    /** @param array{name: string, email: string, mobile?: ?string, password?: ?string, status?: bool} $data */
    public function saveAppAccount(?User $user, array $data, string $roleSlug): User
    {
        $user ??= new User;
        $role = Role::where('slug', $roleSlug)->first();

        if (($data['password'] ?? '') === '' || $data['password'] === null) {
            unset($data['password']); // Empty means "keep".
        }

        $user->fill($data);
        $user->forceFill(['panel_access' => false]);

        if ($role && $user->role_id !== $role->id) {
            $user->role_id = $role->id;
            $user->permissions = $role->permissions;
        }

        $user->save();

        return $user;
    }

    public function delete(User $user): void
    {
        $user->delete();
    }
}
