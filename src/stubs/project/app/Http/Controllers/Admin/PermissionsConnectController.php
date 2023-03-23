<?php

namespace App\Http\Controllers\Admin;

use App\Models\Role;
use App\Models\User;
use Illuminate\Http\Request;
use App\Models\Permission;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\View;
use App\Http\Controllers\Controller;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;

class PermissionsConnectController extends Controller {
    public function __construct() {
        if(!request()->route()) return;

        $this->table = Permission::getModel()->getTable();
        $this->roles_table = Role::getModel()->getTable();
        $this->routeNamespace = Str::before(request()->route()->getName(), '.permissions_connect');
        View::composer('admin/permissions/*', function($view)  {
            $viewData = [
                'route_namespace' => $this->routeNamespace,
            ];
            // @HOOK_VIEW_COMPOSERS
            $view->with($viewData);
        });
        // @HOOK_CONSTRUCT
    }

    public function index() {
        $viewData = [];
        $viewData['guards'] = array_keys( config("auth.guards") );
        $viewData['roles'] = Role::query()
            ->orderBy($this->roles_table.".id", 'ASC')
            ->get();
        $viewData['chRole'] = $viewData['roles']->first();
        if(session()->has('changedRoleId')) {
            if($role = Role::find((int)session()->get('changedRoleId'))) {
                $viewData['chRole'] = $role;
            }
        }
        $usedGuard = $viewData['chRole']->guard_name;
        $viewData['permissions'] = $this->groupingPermissions( Permission::where('guard_name',$usedGuard )
            ->orderBy($this->table.".id", 'ASC')
            ->get() );


        if(session()->has('changedUserId')) {
            $chUser = User::find((int)session()->get('changedUserId'));
            $viewData['chUser'] = $chUser;
            $viewData['rolesPermissionIds'] = $chUser->getPermissionsViaRoles()->pluck('id');
            if($chUser->hasRole('Super Admin', 'admin')) {
                $viewData['checkedPermissions'] = $viewData['permissions'];
            } else {
                $viewData['checkedPermissions'] = $this->groupingPermissions( $chUser->getAllPermissions()
                    ->filter(function($permission) use ($usedGuard) {
                        return $permission->guard_name  == $usedGuard;
                    })->sortBy('id') );
            }
        } else {
            if($viewData['chRole']->name == 'Super Admin' && $viewData['chRole']->guard_name == 'admin') {
                $viewData['rolesPermissionIds'] = collect($viewData['permissions'])->flatten()->pluck('id');
                $viewData['checkedPermissions'] = $viewData['permissions'];
            } else {
                $viewData['checkedPermissions'] = $this->groupingPermissions($viewData['chRole']->permissions()
                    ->orderBy($this->table . ".id", 'ASC')
                    ->get());
            }
        }

        // @HOOK_INDEX

        return view('admin/permissions/permissions_connect', $viewData);
    }

    public function autocomplete($type) {
        if(!request()->has('search'))  abort(402);
        $search = request()->get('search');
        if($type == 'users') {
            $bldQry = User::query();
            if(is_numeric($search)) {
                $bldQry->whereId((int)$search);
            } else {
                $searchParts = explode(' ', $search);
                $searchParts = array_filter($searchParts, 'trim');
                if(empty($searchParts)) abort(402);
                $bldQry->whereHas('addresses', function($qry) use ($searchParts) {
                    $qry->whereRaw('0 = 1');
                    foreach($searchParts as $searchPart) {
                        $qry->orWhere("fname", 'LIKE', "%{$searchPart}%");
                        $qry->orWhere("lname", 'LIKE', "%{$searchPart}%");
                        $qry->orWhere("email", 'LIKE', "%{$searchPart}%");
                    }
                });
            }

            // @HOOK_AUTOCOMPLETE_USERS
            return response()->json( $bldQry->limit(5)->get()->map(function ($user) {
                return [
                    'value' => $user->id,
                    'label' => $user->address? $user->address->fullName."[".$user->email."]" : 'N/A',
                    'url' => route("{$this->routeNamespace}.users.edit", [$user]),
                    'addr' => [
                        'fname' => $user->address?->fname,
                        'lname' => $user->address?->lname,
                        'phone' => $user->address?->phone,
                        'email' => $user->address?->email,
                        'street' => $user->address?->street,
                        'postcode' => $user->address?->postcode,
                        'city' => $user->address?->city,
                        'country' => $user->address?->country,
                        'company' => $user->address?->company,
                        'orgnum' => $user->address?->orgnum,
                    ]
                ];
            })->all() );
        }
        // @HOOK_AUTOCOMPLETE

        abort(402);
    }

    private function groupingPermissions($permissions) {
        $return = [];
        foreach($permissions as $permission) {
            $nameParts = explode('.', $permission->name);
            $nameGroup = \Illuminate\Support\Str::plural($nameParts[0]);
            if(!isset($return[$nameGroup])) $return[$nameGroup] = [];
            $return[$nameGroup][] = $permission;
        }
        return $return;
    }

    public function permissions() {
        $viewData = [];
        if(!request()->has('role_id') || !($viewData['chRole'] = Role::find((int)request()->get('role_id'))) )
            abort(402);
        $viewData['inputBag'] = 'permissions';
        $usedGuard = $viewData['chRole']->guard_name;
        $viewData['permissions'] = Permission::where('guard_name', $usedGuard);
        $viewData['permissions'] = $this->groupingPermissions( $viewData['permissions']
            ->orderBy($this->table.".id", 'ASC')
            ->get() );
        if(request()->has('user_id') && ($chUser = User::find((int)request()->get('user_id')))) {
            $viewData['chUser'] = $chUser;
            $viewData['rolesPermissionIds'] = $chUser->getPermissionsViaRoles()->pluck('id');
            if($chUser->hasRole('Super Admin', 'admin')) {
                $viewData['checkedPermissions'] = $viewData['permissions'];
            } else {
                $viewData['checkedPermissions'] = $this->groupingPermissions( $chUser->getAllPermissions()
                    ->filter(function($permission) use ($usedGuard) {
                        return $permission->guard_name  == $usedGuard;
                    })->sortBy('id') );
            }
        } else {
            if($viewData['chRole']->name == 'Super Admin' && $viewData['chRole']->guard_name == 'admin') {
                $viewData['rolesPermissionIds'] = collect($viewData['permissions'])->flatten()->pluck('id');
                $viewData['checkedPermissions'] = $viewData['permissions'];
            } else {
                $viewData['checkedPermissions'] = $this->groupingPermissions( $viewData['chRole']->permissions()
                    ->orderBy($this->table . ".id", 'ASC')
                    ->get() );
            }
        }

        // @HOOK_PERMISSIONS
        return view('admin/permissions/permissions_connect_permissions', $viewData);

    }

    public function update(Request $request) {
        $inputs = $request->all();
        $inputs = $inputs['permissions']?? [];
        if(empty($inputs)) {
            throw ValidationException::withMessages([
                'no_data' => trans('admin/permissions/validation.no_data'),
            ]);
        }
        $messages = Arr::dot((array)trans('admin/permissions/validation.permissions_connect'));
        $validatedMergeData = [];
        $rules = [
            'role_id' => ['required', function($attribute, $value, $fail) use (&$validatedMergeData) {
                if(!($validatedMergeData['role'] = Role::find((int)$value))) {
                    return $fail(trans('admin/permissions/validation.permissions_connect.role_id.required'));
                }
            }],
            'user_id' => ['nullable', function($attribute, $value, $fail)use (&$validatedMergeData) {
                if(!($validatedMergeData['user'] = User::find((int)$value))) {
                    return $fail(trans('admin/permissions/validation.permissions_connect.user_id.not_found'));
                }
            }],
            'permissions' => ['nullable']
        ];

        // @HOOK_VALIDATE

        $validatedData = array_merge(Validator::make($inputs, $rules, $messages)->validateWithBag('permissions'), $validatedMergeData);
        if(isset($validatedData['user'])) {
            if(!$validatedData['user']->hasRole('Super Admin', 'admin')) {
                $validatedData['user']->permissions()->detach($validatedData['user']->permissions()
                    ->where('guard_name', $validatedData['role']->guard_name)->get()->pluck('id')->toArray());
                $validatedData['user']->givePermissionTo($validatedData['permissions']?? []);
            }
        } else {
            if(!($validatedData['role']->name == 'Super Admin' && $validatedData['role']->guard_name == 'admin')) {
                $validatedData['role']->syncPermissions(array_keys($validatedData['permissions']?? []) );
            }
        }
        app()[\Spatie\Permission\PermissionRegistrar::class]->forgetCachedPermissions();
        // @HOOK_UPDATE_VALIDATE

        // @HOOK_UPDATE_END

        event( 'permissions_connect.submited', [$validatedData] );
        return back()->with('message_success', trans('admin/permissions/permissions_connect.updated'))
                ->with('changedRoleId', $validatedData['role_id'])
                ->with('changedUserId', $validatedData['user_id']);
    }
}
