@pushonceOnReady('below_js_on_ready')
<script>
    //CHANGE FILTER
    $(document).on('change', '.js_filter', function(e) {
        var $this = $(this);
        var $thisVal = $this.val();
        if($thisVal == 'all') {
            window.location.href= $this.attr('data-action_all')
            return;
        }
        window.location.href= $this.attr('data-action').replace('__VAL__', $this.val());
    });
</script>
@endpushonceOnReady

{{-- @HOOK_SCRIPTS --}}

<x-admin.main>
    <div class="container-fluid">
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="{{route("{$route_namespace}.home")}}"><i class="fa fa-home"></i></a></li>
            <li class="breadcrumb-item active">@lang('admin/permissions/roles.roles')</li>
        </ol>

        <div class="row">
            <div class="col-12">
                @can('create', App\Models\Role::class)
                    <a href="{{ route("{$route_namespace}.roles.create") }}"
                       class="btn btn-sm btn-primary h5"
                       title="create">
                        <i class="fa fa-plus mr-1"></i>@lang('admin/permissions/roles.create')
                    </a>
                @endcan

                {{-- @HOOK_ADDON_LINKS --}}

            </div>
        </div>

        <form autocomplete="off">
            <div class="row">
                {{-- GUARDS--}}
                <div class="form-group row col-lg-5">
                    <label for="filters[guard]" class="col-form-label col-sm-2">@lang('admin/permissions/roles.filter_guards'):</label>
                    <div class="col-sm-10">
                        <select id="filters[guard]"
                                name="filters[guard]"
                                data-action_all="{{marinarFullUrlWithQuery( ['filters' => ['guard' => null]] )}}"
                                data-action="{{marinarFullUrlWithQuery( ['filters' => ['guard' => '__VAL__']] )}}"
                                class="form-control js_filter">
                            <option value='all'>@lang('admin/permissions/roles.filter_guards_all')</option>
                            @php $filters['guard'] = $filters['guard']?? 'all'; @endphp
                            @foreach($guards as $guard)
                                <option value="{{ $guard }}"
                                        @if($filters['guard'] === $guard) selected="selected" @endif
                                >{{$guard}}
                            @endforeach
                        </select>
                    </div>
                </div>
                {{-- END GUARDS--}}

                {{-- @HOOK_AFTER_GUARDS_FILTER --}}

            </div>

        </form>

        <x-admin.box_messages />

        <div class="table-responsive rounded ">
            <table class="table table-sm">
                <thead class="thead-light">
                <tr class="">
                    <th scope="col" class="text-center">@lang('admin/permissions/roles.id')</th>
                    {{-- @HOOK_AFTER_ID_TH --}}
                    <th scope="col">@lang('admin/permissions/roles.name')</th>
                    {{-- @HOOK_AFTER_NAME_TH --}}
                    <th scope="col w-50">@lang('admin/permissions/roles.show_name')</th>
                    {{-- @HOOK_AFTER_SHOW_NAME_TH --}}
                    <th scope="col">@lang('admin/permissions/roles.guard')</th>
                    {{-- @HOOK_AFTER_GUARD_TH --}}
                    <th scope="col" class="text-center">@lang('admin/permissions/roles.edit')</th>
                    {{-- @HOOK_AFTER_EDIT_TH --}}
                    <th scope="col" class="text-center">@lang('admin/permissions/roles.remove')</th>
                </tr>
                </thead>
                <tbody>
                @forelse($roles as $role)
                    @php
                        $editUri = route("{$route_namespace}.roles.edit", $role);
                    @endphp
                    <tr data-id="{{$role->id}}"
                        data-show="1">
                        <td scope="row" class="text-center align-middle"><a href="{{ $editUri }}"
                                                                            title="@lang('admin/permissions/roles.edit')"
                            >{{ $role->id }}</a></td>

                        {{-- @HOOK_AFTER_ID --}}

                        {{--    NAME    --}}
                        <td class="align-middle">
                            <a href="{{ $editUri }}"
                               title="{{$role->name}}"
                            >{{ $role->name }}</a></td>

                        {{-- @HOOK_AFTER_NAME --}}

                        {{--    SHOW NAME    --}}
                        <td class="align-middle w-50">
                            <a href="{{ $editUri }}"
                               title="{{$role->aVar('name')}}"
                            >{{ \Illuminate\Support\Str::words($role->aVar('name'), 40,'...') }}</a></td>

                        {{-- @HOOK_AFTER_SHOW_NAME --}}

                        {{--    GUARD    --}}
                        <td class="align-middle">{{ $role->guard_name }}</td>
                        {{-- @HOOK_AFTER_GUARD --}}

                        {{--    EDIT    --}}
                        <td class="text-center">
                            <a class="btn btn-link text-success"
                               href="{{ $editUri }}"
                               title="@lang('admin/permissions/roles.edit')"><i class="fa fa-edit"></i></a></td>

                        {{-- @HOOK_AFTER_EDIT --}}

                        {{--    DELETE    --}}
                        <td class="text-center">
                            @can('delete', $role)
                                <form action="{{ route("{$route_namespace}.roles.destroy", $role->id) }}"
                                      method="POST"
                                      id="delete[{{$role->id}}]">
                                    @csrf
                                    @method('DELETE')
                                    @php
                                        $redirectTo = (!$roles->onFirstPage() && $roles->count() == 1)?
                                                $roles->previousPageUrl() :
                                                url()->full();
                                    @endphp
                                    <input type="hidden" name="redirect_to" value="{{$redirectTo}}" />
                                    <button class="btn btn-link text-danger"
                                            title="@lang('admin/permissions/roles.remove')"
                                            onclick="if(confirm('@lang("admin/permissions/roles.remove_ask")')) document.querySelector( '#delete\\[{{$role->id}}\\] ').submit() "
                                            type="button"><i class="fa fa-trash"></i></button>
                                </form>
                            @endcan
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="100%">@lang('admin/permissions/roles.no_roles')</td>
                    </tr>
                @endforelse
                </tbody>
            </table>

            {{$roles->links('admin.paging')}}

        </div>
    </div>
</x-admin.main>
