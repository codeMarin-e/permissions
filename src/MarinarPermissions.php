<?php
    namespace Marinar\Permissions;

    use Marinar\Permissions\Database\Seeders\MarinarPermissionsInstallSeeder;

    class MarinarPermissions {

        public static function getPackageMainDir() {
            return __DIR__;
        }

        public static function injects() {
            return MarinarPermissionsInstallSeeder::class;
        }
    }
