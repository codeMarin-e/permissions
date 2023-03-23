@if($authUser->can('view', \App\Models\Permission::class) || $authUser->can('view', \App\Models\Role::class) || $authUser->can('permissions_connect_view'))
    @php $groupActive = (request()->route()->named(["{$whereIam}.permissions.*", "{$whereIam}.roles.*", "{$whereIam}.permissions_connect.*"])); @endphp
    <li class="nav-item @if($groupActive) active show @endif dropdown">
        <a class="nav-link dropdown-toggle"
           href="#" id="modulesProductsDropdown"
           role="button"
           data-toggle="dropdown"
           aria-haspopup="true"
           aria-expanded="@if($groupActive) active @else false @endif">
            <i class="fas fa-fw fa-key"></i>
            <span>@lang('admin/permissions/permissions_connect.sidebar_group')</span>
        </a>
        <div class="dropdown-menu @if($groupActive) show @endif" aria-labelledby="modulesProductsDropdown">
            @can('view', \App\Models\Role::class)
                <a class="dropdown-item" href="{{route("{$whereIam}.roles.index")}}">@lang("admin/permissions/roles.sidebar")</a>
            @endcan
            @can('view', \App\Models\Permission::class)
                <a class="dropdown-item" href="{{route("{$whereIam}.permissions.index")}}">@lang("admin/permissions/permissions.sidebar")</a>
            @endcan
            @can('permissions_connect_view')
                <a class="dropdown-item" href="{{route("{$whereIam}.permissions_connect.index")}}">@lang("admin/permissions/permissions_connect.sidebar")</a>
            @endcan
            {{-- @HOOK_OPTIONS --}}
        </div>
    </li>
@endif
