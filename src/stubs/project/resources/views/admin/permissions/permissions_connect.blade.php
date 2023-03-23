@php $inputBag = 'permissions'; @endphp

@pushonce('above_css')
<!-- JQUERY UI -->
<link href="{{ asset('admin/vendor/jquery-ui-1.12.1/jquery-ui.min.css') }}" rel="stylesheet" type="text/css" />
@endpushonce

@pushonce('below_js')
<script language="javascript"
        type="text/javascript"
        src="{{ asset('admin/vendor/jquery-ui-1.12.1/jquery-ui.min.js') }}"></script>
@endpushonce

@pushonceOnReady('below_js_on_ready')
<script>
    var roleInput = '[name="{{$inputBag}}\\[role_id\\]"]';
    var inputUser = 'input[name="{{$inputBag}}\\[user_id\\]"]';
    var $inputUser = $(inputUser);

    $(document).on('change', roleInput, function() {
        var $this = $(this);
        $(document).trigger('refresh_permissions');
    });
    $inputUser.on('change', function() {
        var $this = $(this);
        $(document).trigger('refresh_permissions');
        if($this.val() == '') {
            $('#autocomplete_user_url').first().attr('href', "javascript:void(0)").removeAttr('target');;
        }
    })
    var $autocompleteUsers = $('#autocomplete_user');
    $autocompleteUsers.autocomplete({
        source: function( request, response ) {
            $.getJSON( $autocompleteUsers.attr('data-src'), {
                search: request.term,
            }, response );
        },
        change: function( event, ui ) {
            if(this.value.length) return;
            $inputUser.val("").trigger("change");
            if($('#autocomplete_user_url').length)
                $('#autocomplete_user_url').first().attr('href', "javascript:void(0)").removeAttr('target');
        },


        search: function() {
            $inputUser.val("").trigger("change");
            if($('#autocomplete_user_url').length)
                $('#autocomplete_user_url').first().attr('href', "javascript:void(0)").removeAttr('target');;
            // if ( this.value.length < 2 ) {
            //
            //     return false;
            // }
        },
        focus: function() {
            // prevent value inserted on focus
            return false;
        },
        select: function( event, ui ) {
            $inputUser.val( ui.item.value ).trigger("change");;
            this.value = ui.item.label;

            if(ui.item.url && $('#autocomplete_user_url').length) {
                $('#autocomplete_user_url').first().attr('href', ui.item.url).attr('target','_blank');;
            }
            // setTimeout(function () {
            //     $(event.target).blur();
            // });
            return false;
        }
    });
    @if(isset($changedUserId) || isset($changedRoleId))
        $(document).trigger('refresh_permissions');
    @endif
</script>
@endpushonceOnReady

@pushonce('below_templates')
<div id="js_loader" class="d-none">
    <div class="spinner-border spinner-border-sm text-warning" role="status">
        <span class="sr-only">Loading...</span>
    </div>
</div>
@endpushonce

{{-- @HOOK_SCRIPTS --}}

<x-admin.main>
    <div class="container-fluid">
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="{{route("{$route_namespace}.home")}}"><i class="fa fa-home"></i></a></li>
            <li class="breadcrumb-item"><a href="{{ route("{$route_namespace}.permissions_connect.index") }}">@lang('admin/permissions/permissions_connect.title')</a></li>
        </ol>

        <div class="card">
            <div class="card-body">
                <form action="{{ route("{$route_namespace}.permissions_connect.update") }}"
                      method="POST"
                      autocomplete="off"
                      enctype="multipart/form-data">
                    @csrf
                    @method('PATCH')

                    <x-admin.box_messages />

                    <x-admin.box_errors :inputBag="$inputBag" />

                    {{-- @HOOK_BEGINNING --}}
                    <div class="form-group row">
                        <div class="col-lg-4">
                            <div class="form-group row">
                                <div class="col-lg-12">
                                    <input type="hidden"
                                           name="{{$inputBag}}[user_id]"
                                           value='{{ old("{$inputBag}.user_id", isset($chUser)? $chUser->id : null )}}'
                                    />
                                    <div class="input-group">
                                        <input type="text"
                                               class="form-control @if($errors->$inputBag->has('user_id')) is-invalid @endif"
                                               onkeyup="this.classList.remove('is-invalid')"
                                               id="autocomplete_user"
                                               name="autocomplete_user"
                                               placeholder="@lang('admin/permissions/permissions_connect.user')"
                                               value="{{ old("{$inputBag}.autocomplete_user", isset($chUser)? $chUser->getAddress()->fullname."[".$chUser->email."]" : null )}}"
                                               data-src="{{route("{$route_namespace}.permissions_connect.autocomplete", ['users'])}}"
                                        />
                                        @can('view', \App\Models\User::class)
                                            <div class="input-group-append" >
                                                <a href="javascript:void(0)"
                                                   class="btn btn-primary"
                                                   id="autocomplete_user_url">@lang('admin/permissions/permissions_connect.check_user')</a>
                                            </div>
                                        @endcan
                                    </div>

                                </div>
                            </div>
                        </div>
                        <div class="col-lg-4">
                            @php
                                $sRole= old("{$inputBag}.role_id", $chRole->id);
                            @endphp
                            <div class="row">
                                <label for="{{$inputBag}}[role_id]"
                                       class="col-lg-2 col-form-label">@lang('admin/permissions/permissions_connect.role'):</label>
                                <div class="col-lg-10">
                                    <select class="form-control @if($errors->$inputBag->has('role_id')) is-invalid @endif"
                                            id="{{$inputBag}}[role_id]"
                                            name="{{$inputBag}}[role_id]">
                                        @foreach($roles as $role)
                                            @php $roleName = $role->aVar('name')? $role->aVar('name') : $role->name; @endphp
                                            <option value="{{$role->id}}"
                                                    @if($sRole === $role->id)selected="selected"@endif
                                            >{{$roleName}}[{{$role->guard_name}}]</option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                        </div>
{{--                        <div class="col-lg-4">--}}
{{--                            @php--}}
{{--                                $sGuard = old("{$inputBag}.guard_name", reset($guards));--}}
{{--                            @endphp--}}
{{--                            <div class="row">--}}
{{--                                <label for="{{$inputBag}}[guard_name]"--}}
{{--                                       class="col-lg-3 col-form-label">@lang('admin/permissions/permissions_connect.guard_name'):</label>--}}
{{--                                <div class="col-lg-9">--}}
{{--                                    <select class="form-control @if($errors->$inputBag->has('guard_name')) is-invalid @endif"--}}
{{--                                            id="{{$inputBag}}[guard_name]"--}}
{{--                                            name="{{$inputBag}}[guard_name]">--}}
{{--                                        @foreach($guards as $guard)--}}
{{--                                            <option value="{{$guard}}"--}}
{{--                                                    @if($sGuard === $guard)selected="selected"@endif--}}
{{--                                            >{{$guard}}</option>--}}
{{--                                        @endforeach--}}
{{--                                    </select>--}}
{{--                                </div>--}}
{{--                            </div>--}}
{{--                        </div>--}}
                    </div>
                    {{-- @HOOK_AFTER_FILTERS --}}

                    @include('admin/permissions/permissions_connect_permissions', [
                        'permissions' => $permissions,
                        'checkedPermissions' => $checkedPermissions
                    ])

                    <div class="form-group row">
                        @can('permissions_connect_update')
                            <button class='btn btn-info mr-2'
                                    type='submit'
                                    name='update'>@lang('admin/permissions/permissions_connect.update')</button>
                        @endcan
                        <a class='btn btn-warning'
                           href="{{ route("{$route_namespace}.permissions_connect.index") }}"
                        >@lang('admin/permissions/permissions_connect.cancel')</a>
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
