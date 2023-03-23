<?php
namespace Database\Seeders\Packages\Permissions;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Permission;

class MarinarPermissionsSeeder extends Seeder {

    public function run() {
        app()[\Spatie\Permission\PermissionRegistrar::class]->forgetCachedPermissions();
        Permission::upsert([
            ['guard_name' => 'admin', 'name' => 'permissions_connect.view'],
            ['guard_name' => 'admin', 'name' => 'permissions_connect.update'],

            ['guard_name' => 'admin', 'name' => 'roles.view'],
            ['guard_name' => 'admin', 'name' => 'role.create'],
            ['guard_name' => 'admin', 'name' => 'role.update'],
            ['guard_name' => 'admin', 'name' => 'role.delete'],

            ['guard_name' => 'admin', 'name' => 'permissions.view'],
            ['guard_name' => 'admin', 'name' => 'permission.create'],
            ['guard_name' => 'admin', 'name' => 'permission.update'],
            ['guard_name' => 'admin', 'name' => 'permission.delete'],
        ], ['guard_name','name']);
    }
}
