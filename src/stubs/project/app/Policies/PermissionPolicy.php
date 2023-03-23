<?php

namespace App\Policies;

use App\Models\Permission;
use App\Models\User;

class PermissionPolicy
{
    public function before(User $user, $ability) {
        // @HOOK_POLICY_BEFORE
        if($user->hasRole('Super Admin', 'admin') )
            return true;
    }

    public function view(User $user) {
        // @HOOK_POLICY_VIEW
        return $user->hasPermissionTo('permissions.view', request()->whereIam());
    }

    public function create(User $user) {
        // @HOOK_POLICY_CREATE
        return $user->hasPermissionTo('permission.create', request()->whereIam());
    }

    public function update(User $user, Permission $permission) {
        // @HOOK_POLICY_UPDATE
        if( !$user->hasPermissionTo('permission.update', request()->whereIam()) )
            return false;
        return true;
    }

    public function delete(User $user, Permission $permission) {
        // @HOOK_POLICY_DELETE
        if( !$user->hasPermissionTo('permission.delete', request()->whereIam()) )
            return false;
        return true;
    }

    // @HOOK_POLICY_END
}
