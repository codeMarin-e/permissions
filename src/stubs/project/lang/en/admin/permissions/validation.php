<?php
return \Illuminate\Support\Arr::undot([
    'no_inputs' => 'No data sent',
    'permissions' => \Illuminate\Support\Arr::undot([
        'guard.in' => 'There is no such guard',
        'name.required' => 'The filed `Name` is required',
        'name.max' => 'The filed `Name` is too long',
        'add.name.required' => 'The filed `Show Name` is required',
        'add.name.max' => 'The filed `Show Name` is too long',
    ]),
    'roles' => \Illuminate\Support\Arr::undot([
        'guard.in' => 'There is no such guard',
        'name.required' => 'The filed `Name` is required',
        'name.max' => 'The filed `Name` is too long',
        'add.name.required' => 'The filed `Show Name` is required',
        'add.name.max' => 'The filed `Show Name` is too long',
    ]),
    'permissions_connect' => \Illuminate\Support\Arr::undot([
        'role_id.required' => '`Role ID` is required',
        'user_id.not_found' => 'User is not found',
    ]),

    //@HOOK_LANG
]);
