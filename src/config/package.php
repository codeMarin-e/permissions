<?php
	return [
		'install' => [
            'php artisan db:seed --class="\Marinar\Permissions\Database\Seeders\MarinarPermissionsInstallSeeder"',
		],
		'remove' => [
            'php artisan db:seed --class="\Marinar\Permissions\Database\Seeders\MarinarPermissionsRemoveSeeder"',
        ]
	];
