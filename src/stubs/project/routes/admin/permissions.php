<?php

use \App\Http\Controllers\Admin\PermissionsController;
use \App\Http\Controllers\Admin\PermissionsConnectController;
use \App\Http\Controllers\Admin\RolesController;
use \App\Models\Role;
use \App\Models\Permission;

Route::group([
    'controller' => RolesController::class,
    'middleware' => ['auth:admin', 'can:view,' . Role::class],
    'as' => 'roles.', //naming prefix
    'prefix' => 'roles', //for routes
], function() {
    Route::get('', 'index')->name('index');
    Route::post('', 'store')->name('store')->middleware('can:create,' . Role::class);
    Route::get('create', 'create')->name('create')->middleware('can:create,' . Role::class);
    Route::get('{chRole}/edit', 'edit')->name('edit');
    Route::get('{chRole}', 'edit')->name('show');

    // @HOOK_ROLES_ROUTES_MODEL

    Route::patch('{chRole}', 'update')->name('update')->middleware('can:update,chRole');
    Route::delete('{chRole}', 'destroy')->name('destroy')->middleware('can:delete,chRole');

    // @HOOK_ROLES_ROUTES
});

Route::group([
    'controller' => PermissionsController::class,
    'middleware' => ['auth:admin', 'can:view,' . Permission::class],
    'as' => 'permissions.', //naming prefix
    'prefix' => 'permissions', //for routes
], function() {
    Route::get('', 'index')->name('index');
    Route::post('', 'store')->name('store')->middleware('can:create,' . Permission::class);
    Route::get('create', 'create')->name('create')->middleware('can:create,' . Permission::class);
    Route::get('{chPermission}/edit', 'edit')->name('edit');
    Route::get('{chPermission}', 'edit')->name('show');

    // @HOOK_PERMISSIONS_ROUTES_MODEL

    Route::patch('{chPermission}', 'update')->name('update')->middleware('can:update,chPermission');
    Route::delete('{chPermission}', 'destroy')->name('destroy')->middleware('can:delete,chPermission');

    // @HOOK_PERMISSIONS_ROUTES

});

Route::group([
    'controller' => PermissionsConnectController::class,
    'middleware' => ['auth:admin', 'can:permissions_connect_view'],
    'as' => 'permissions_connect.', //naming prefix
    'prefix' => 'permissions_connect', //for routes
], function() {
    Route::get('', 'index')->name('index');

    // @HOOK_ROUTES_MODEL

    Route::patch('', 'update')->name('update')->middleware('can:permissions_connect_update');
    Route::get('autocomplete/{type}', 'autocomplete')->name('autocomplete');
    Route::get('permissions', 'permissions')->name('permissions');

    // @HOOK_ROUTES
});
