<?php
    namespace Marinar\Permissions\Database\Seeders;

    use Illuminate\Database\Seeder;
    use Marinar\Permissions\MarinarPermissions;

    class MarinarPermissionsInstallSeeder extends Seeder {

        use \Marinar\Marinar\Traits\MarinarSeedersTrait;

        public static function configure() {
            static::$packageName = 'marinar_permissions';
            static::$packageDir = MarinarPermissions::getPackageMainDir();
        }

        public function run() {
            if(!in_array(env('APP_ENV'), ['dev', 'local'])) return;

            $this->autoInstall();

            $this->refComponents->info("Done!");
        }

    }
