@php $inputBag = 'permission'; $chPermission = $chPermission?? null; @endphp

@pushonce('below_templates')
@if(isset($chPermission) && $authUser->can('delete', $chPermission))
    <form action="{{ route("{$route_namespace}.permissions.destroy", $chPermission) }}"
          method="POST"
          id="delete[{{$chPermission->id}}]">
        @csrf
        @method('DELETE')
    </form>
@endif
@endpushonce

{{-- @HOOK_SCRIPTS --}}

<x-admin.main>
    <div class="container-fluid">
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="{{route("{$route_namespace}.home")}}"><i class="fa fa-home"></i></a></li>
            <li class="breadcrumb-item"><a href="{{ route("{$route_namespace}.permissions.index") }}">@lang('admin/permissions/permissions.permissions')</a></li>
            <li class="breadcrumb-item active">@isset($chPermission){{ $chPermission->id }}@else @lang('admin/permissions/permission.create') @endisset</li>
        </ol>

        <div class="card">
            <div class="card-body">
                <form action="@isset($chPermission){{ route("{$route_namespace}.permissions.update", $chPermission) }}@else{{ route("{$route_namespace}.permissions.store") }}@endisset"
                      method="POST"
                      autocomplete="off"
                      enctype="multipart/form-data">
                    @csrf
                    @isset($chPermission)@method('PATCH')@endisset

                    <x-admin.box_messages />

                    <x-admin.box_errors :inputBag="$inputBag" />

                    {{-- @HOOK_BEGINNING --}}

                    @php
                        $sGuard = old("{$inputBag}.guard_name", $chPermission?->guard_name);
                    @endphp
                    <div class="form-group row">
                        <label for="{{$inputBag}}[guard_name]"
                               class="col-lg-2 col-form-label">@lang('admin/permissions/permission.guard_name'):</label>
                        <div class="col-lg-4">
                            <select class="form-control @if($errors->$inputBag->has('guard_name')) is-invalid @endif"
                                    id="{{$inputBag}}[guard_name]"
                                    name="{{$inputBag}}[guard_name]">
                                @foreach($guards as $guard)
                                    <option value="{{$guard}}"
                                            @if($sGuard === $guard)selected="selected"@endif
                                    >{{$guard}}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                    {{-- @HOOK_AFTER_GUARD --}}

                    <div class="form-group row">
                        <label for="{{$inputBag}}[name]"
                               class="col-lg-2 col-form-label"
                        >@lang('admin/permissions/permission.name'):</label>
                        <div class="col-lg-10">
                            <input type="text"
                                   name="{{$inputBag}}[name]"
                                   id="{{$inputBag}}][name]"
                                   value="{{ old("{$inputBag}.name", $chPermission?->name ) }}"
                                   class="form-control @if($errors->$inputBag->has('name')) is-invalid @endif"
                            />
                        </div>
                    </div>
                    {{-- @HOOK_AFTER_NAME --}}

                    <div class="form-group row">
                        <label for="{{$inputBag}}[add][name]"
                               class="col-lg-2 col-form-label"
                        >@lang('admin/permissions/permission.show_name'):</label>
                        <div class="col-lg-10">
                            <input type="text"
                                   name="{{$inputBag}}[add][name]"
                                   id="{{$inputBag}}[add][name]"
                                   value="{{ old("{$inputBag}.add.name", $chPermission?->aVar('name') ) }}"
                                   class="form-control @if($errors->$inputBag->has('add.name')) is-invalid @endif"
                            />
                        </div>
                    </div>
                    {{-- @HOOK_AFTER_SHOW_NAME --}}

                    <div class="form-group row">
                        @isset($chPermission)
                            @can('update', $chPermission)
                                <button class='btn btn-success mr-2'
                                        type='submit'
                                        name='action'>@lang('admin/permissions/permission.save')</button>

                                <button class='btn btn-info mr-2'
                                        type='submit'
                                        name='update'>@lang('admin/permissions/permission.update')</button>
                            @endcan

                            @can('delete', $chPermission)
                                <button class='btn btn-danger mr-2'
                                        type='button'
                                        onclick="if(confirm('@lang("admin/permissions/permission.delete_ask")')) document.querySelector( '#delete\\[{{$chPermission->id}}\\] ').submit() "
                                        name='delete'>@lang('admin/permissions/permission.delete')</button>
                            @endcan
                        @else
                            @can('create', App\Models\Permission::class)
                                <button class='btn btn-success mr-2'
                                        type='submit'
                                        name='create'>@lang('admin/permissions/permission.create')</button>
                            @endcan
                        @endisset
                        <a class='btn btn-warning'
                           href="{{ route("{$route_namespace}.permissions.index") }}"
                        >@lang('admin/permissions/permission.cancel')</a>
                    </div>

                    <div class="form-group row">
                        {{-- @HOOK_ADDON_BUTTONS --}}
                    </div>

                </form>

            </div>
        </div>
        {{-- @HOOK_ADDONS --}}
    </div>
</x-admin.main>
