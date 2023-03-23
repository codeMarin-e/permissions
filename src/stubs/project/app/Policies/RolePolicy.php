<?php

namespace App\Policies;

use App\Models\Role;
use App\Models\User;

class RolePolicy
{
    public function before(User $user, $ability) {
        // @HOOK_POLICY_BEFORE
        if($user->hasRole('Super Admin', 'admin') )
            return true;
    }

    public function view(User $user) {
        // @HOOK_POLICY_VIEW
        return $user->hasRoleTo('permissions.view', request()->whereIam());
    }

    public function create(User $user) {
        // @HOOK_POLICY_CREATE
        return $user->hasRoleTo('permission.create', request()->whereIam());
    }

    public function update(User $user, Role $permission) {
        // @HOOK_POLICY_UPDATE
        if( !$user->hasRoleTo('permission.update', request()->whereIam()) )
            return false;
        return true;
    }

    public function delete(User $user, Role $permission) {
        // @HOOK_POLICY_DELETE
        if( !$user->hasRoleTo('permission.delete', request()->whereIam()) )
            return false;
        return true;
    }

    // @HOOK_POLICY_END
}
