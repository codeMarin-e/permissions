<?php
    namespace Marinar\Permissions\Database\Seeders;

    use Illuminate\Database\Seeder;
    use Marinar\Permissions\MarinarPermissions;
    use Spatie\Permission\Models\Permission;

    class MarinarPermissionsRemoveSeeder extends Seeder {

        use \Marinar\Marinar\Traits\MarinarSeedersTrait;

        public static function configure() {
            static::$packageName = 'marinar_permissions';
            static::$packageDir = MarinarPermissions::getPackageMainDir();
        }

        public function run() {
            if(!in_array(env('APP_ENV'), ['dev', 'local'])) return;

            $this->autoRemove();

            $this->refComponents->info("Done!");
        }

        public function clearMe() {
            $this->refComponents->task("Clear DB", function() {
                Permission::whereIn('name', [
                    'permissions_connect.view',
                    'permissions_connect.update',
                    'roles.view',
                    'role.create',
                    'role.update',
                    'role.delete',
                    'permissions.view',
                    'permission.create',
                    'permission.update',
                    'permission.delete',
                ])
                ->where('guard_name', 'admin')
                ->delete();
                app()[\Spatie\Permission\PermissionRegistrar::class]->forgetCachedPermissions();
                return true;
            });
        }
    }
