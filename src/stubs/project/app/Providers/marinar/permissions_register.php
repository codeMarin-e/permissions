<?php
use App\Models\User;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Route;
use App\Models\Role;
use App\Models\Permission;
use App\Policies\RolePolicy;
use App\Policies\PermissionPolicy;

Route::model('chRole', Role::class);
Route::model('chPermission', Permission::class);
Gate::policy(Role::class, RolePolicy::class);
Gate::policy(Permission::class, PermissionPolicy::class);

Gate::define('permissions_connect_view', function (User $user) {
    if($user->hasRole('Super Admin', 'admin') ) return true;
    return $user->hasPermissionTo('permissions_connect.view', request()->whereIam());
});
Gate::define('permissions_connect_update', function (User $user) {
    if($user->hasRole('Super Admin', 'admin') ) return true;
    return $user->hasPermissionTo('permissions_connect.update', request()->whereIam());
});
