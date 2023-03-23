<?php

namespace App\Http\Controllers\Admin;

use Illuminate\Http\Request;
use App\Models\Permission;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\View;
use App\Http\Controllers\Controller;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;

class PermissionsController extends Controller {
    public function __construct() {
        if(!request()->route()) return;

        $this->table = Permission::getModel()->getTable();
        $this->routeNamespace = Str::before(request()->route()->getName(), '.permissions');
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
        $viewData['permissions'] = Permission::query();

        if($filters = request()->get('filters')) {
            //BY GUARD
            if (isset($filters['guard'])) {
                if ($filters['guard'] === 'all') {
                    $routeQry = request()->query();
                    unset($routeQry['filters']['guard']);
                    return redirect(now_route(queries: $routeQry));
                }
                $filterGuard = $filters['guard'];
                if (in_array($filterGuard, $viewData['guards'])) {
                    $viewData['permissions'] = $viewData['permissions']->where('guard_name', $filterGuard);
                }
                $viewData['filters']['guard'] = $filterGuard;
            }
            //END BY GUARD
        }

        // @HOOK_INDEX

        $viewData['permissions'] = $viewData['permissions']
            ->orderBy($this->table.".id", 'ASC')
            ->paginate(20)->appends( request()->query() );

        return view('admin/permissions/permissions', $viewData);
    }

    public function create() {
        $viewData = [];
        $viewData['guards'] = array_keys( config("auth.guards") );

        // @HOOK_CREATE

        return view('admin/permissions/permission', $viewData);
    }

    public function edit(Permission $chPermission) {
        $viewData = [];
        $viewData['guards'] = array_keys( config("auth.guards") );
        $viewData['chPermission'] = $chPermission;

        // @HOOK_EDIT

        return view('admin/permissions/permission', $viewData);
    }

    private function validateData(&$request, $chPermission = null) {
        $guards = array_keys( config("auth.guards") );
        $inputs = request()->all();
        $inputs = $inputs['permission']?? [];
        if(empty($inputs)) {
            throw ValidationException::withMessages(['permission' => [
                'no_data' => trans('admin/permissions/validation.no_data'),
            ]]);
        }
        $messages = Arr::dot((array)trans('admin/permissions/validation.permissions'));
        $rules = [
            'name' => 'required|max:255',
            'add.name'  => 'nullable|max:255',
            'guard_name' => ['required', Rule::in($guards)]
        ];

        // @HOOK_VALIDATE

        return Validator::make($inputs, $rules, $messages)->validateWithBag('permission');
    }

    public function store(Request $request) {
        $validatedData = $this->validateData($request);

        // @HOOK_STORE_VALIDATE

        $chPermission = Permission::create( $validatedData );
        $chPermission->setAVars( $validatedData['add'] );

        // @HOOK_STORE_END
        event( 'permission.submited', [$chPermission, $validatedData] );

        return redirect()->route($this->routeNamespace.'.permissions.edit', $chPermission)
            ->with('message_success', trans('admin/permissions/permission.created'));
    }

    public function update(Permission $chPermission, Request $request) {
        $validatedData = $this->validateData($request, $chPermission);

        // @HOOK_UPDATE_VALIDATE

        $chPermission->update( $validatedData );
        $chPermission->setAVars( $validatedData['add'] );

        // @HOOK_UPDATE_END

        event( 'permission.submited', [$chPermission, $validatedData] );
        if($request->has('action')) {
            return redirect()->route($this->routeNamespace.'.permissions.index')
                ->with('message_success', trans('admin/permissions/permission.updated'));
        }
        return back()->with('message_success', trans('admin/permissions/permission.updated'));
    }

    public function destroy(Permission $chPermission, Request $request) {
        // @HOOK_DESTROY
        $chPermission->delete();
        // @HOOK_DESTROY_END
        if($request->redirect_to)
            return redirect()->to($request->redirect_to)
                ->with('message_danger', trans('admin/permissions/permission.deleted'));

        return redirect()->route($this->routeNamespace.'.permissions.index')
            ->with('message_danger', trans('admin/permissions/permission.deleted'));
    }
}
