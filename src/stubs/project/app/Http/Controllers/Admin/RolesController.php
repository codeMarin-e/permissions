<?php

namespace App\Http\Controllers\Admin;

use Illuminate\Http\Request;
use App\Models\Role;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\View;
use App\Http\Controllers\Controller;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;

class RolesController extends Controller {
    public function __construct() {
        if(!request()->route()) return;

        $this->table = Role::getModel()->getTable();
        $this->routeNamespace = Str::before(request()->route()->getName(), '.roles');
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
        $viewData['roles'] = Role::query();

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
                    $viewData['roles'] = $viewData['roles']->where('guard_name', $filterGuard);
                }
                $viewData['filters']['guard'] = $filterGuard;
            }
            //END BY GUARD
        }

        // @HOOK_INDEX

        $viewData['roles'] = $viewData['roles']
            ->orderBy($this->table.".id", 'ASC')
            ->paginate(20)->appends( request()->query() );

        return view('admin/permissions/roles', $viewData);
    }

    public function create() {
        $viewData = [];
        $viewData['guards'] = array_keys( config("auth.guards") );

        // @HOOK_CREATE

        return view('admin/permissions/role', $viewData);
    }

    public function edit(Role $chRole) {
        $viewData = [];
        $viewData['guards'] = array_keys( config("auth.guards") );
        $viewData['chRole'] = $chRole;

        // @HOOK_EDIT

        return view('admin/permissions/role', $viewData);
    }

    private function validateData(&$request, $chRole = null) {
        $guards = array_keys( config("auth.guards") );
        $inputs = request()->all();
        $inputs = $inputs['role']?? [];
        if(empty($inputs)) {
            throw ValidationException::withMessages(['role' => [
                'no_data' => trans('admin/permissions/validation.no_data'),
            ]]);
        }
        $messages = Arr::dot((array)trans('admin/permissions/validation.roles'));
        $rules = [
            'name' => 'required|max:255',
            'add.name'  => 'nullable|max:255',
            'guard_name' => ['required', Rule::in($guards)]
        ];

        // @HOOK_VALIDATE

        return Validator::make($inputs, $rules, $messages)->validateWithBag('role');
    }

    public function store(Request $request) {
        $validatedData = $this->validateData($request);

        // @HOOK_STORE_VALIDATE

        $chRole = Role::create( $validatedData );
        $chRole->setAVars( $validatedData['add'] );

        // @HOOK_STORE_END
        event( 'role.submited', [$chRole, $validatedData] );

        return redirect()->route($this->routeNamespace.'.roles.edit', $chRole)
            ->with('message_success', trans('admin/permissions/role.created'));
    }

    public function update(Role $chRole, Request $request) {
        $validatedData = $this->validateData($request, $chRole);

        // @HOOK_UPDATE_VALIDATE

        $chRole->update( $validatedData );
        $chRole->setAVars( $validatedData['add'] );

        // @HOOK_UPDATE_END

        event( 'role.submited', [$chRole, $validatedData] );
        if($request->has('action')) {
            return redirect()->route($this->routeNamespace.'.roles.index')
                ->with('message_success', trans('admin/permissions/role.updated'));
        }
        return back()->with('message_success', trans('admin/permissions/role.updated'));
    }

    public function destroy(Role $chRole, Request $request) {
        // @HOOK_DESTROY
        $chRole->delete();
        // @HOOK_DESTROY_END
        if($request->redirect_to)
            return redirect()->to($request->redirect_to)
                ->with('message_danger', trans('admin/permissions/role.deleted'));

        return redirect()->route($this->routeNamespace.'.roles.index')
            ->with('message_danger', trans('admin/permissions/role.deleted'));
    }
}
