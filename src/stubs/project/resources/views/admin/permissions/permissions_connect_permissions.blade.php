@pushonce('below_js')
<script language="javascript"
        type="text/javascript"
        src="{{ asset('admin/vendor/jquery-ui-1.12.1/jquery-ui.min.js') }}"></script>
@endpushonce
@pushonceOnReady('below_js_on_ready')
<script>
    $(document).on('change', '.js_permissions_all', function() {
        var $this = $(this);
        var group = $this.attr('data-group');
        if($this.prop('checked')) {
            $('input[data\\-group="'+ group +'"]').prop('checked', true);
        }
    });
    $(document).on('change', '.js_permission', function() {
        var $this = $(this);
        var group = $this.attr('data-group');
        if(!$this.prop('checked')) {
            $('.js_permissions_all[data\\-group="'+ group +'"]').prop('checked', false);
        }
    });

    var refreshingPermissions = false;
    $(document).on('refresh_permissions', function() {
        if (refreshingPermissions) return;
        refreshingPermissions = true;
        var $container = $('#js_permissions');
        $container.html($('#js_loader').html());
        var data = {
            'role_id': $('[name="{{$inputBag}}\\[role_id\\]"]').val(),
        }
        var user_id;
        if(user_id = $('[name="{{$inputBag}}\\[user_id\\]"]').val()) {
            data['user_id'] = user_id;
        }
        $.ajax({
            method: 'GET',
            url: $container.attr('data-src'),
            timeout: 0,
            data: data,
            dataType: 'html',
            error: function (jqXHR, textStatus, errorThrown) {
                if (jqXHR.responseJSON && jqXHR.responseJSON.message) alert(jqXHR.responseJSON.message);
                else alert('Error');
            },
            success: function (response) {
                $container.html($(response).filter('#js_permissions').first().html());
                refreshingPermissions = false;
            }
        });
    });
</script>
@endpushonceOnReady

{{-- @HOOK_SCRIPTS --}}


<div id="js_permissions"
     class="row"
     data-src="{{route("{$route_namespace}.permissions_connect.permissions")}}">
    @foreach($permissions as $group => $groupedPermissions)
        @php $checkedInThisGroup = collect($checkedPermissions[$group]?? [])->pluck('id');
        @endphp
        <div class="col-3">
            <div class="card" style="margin-bottom: 15px;">
                <div class="card-header">
                    <div>
                        <div class="form-check form-check-inline">
                            <input type="checkbox"
                                   name="{{$inputBag}}[permissions_all][{{$group}}]"
                                   id="{{$inputBag}}[permissions_all][{{$group}}]"
                                   value="1"
                                   data-group="{{$group}}"
                                   class="js_permissions_all form-check-input"
                                   @if(old("{$inputBag}.permissions_all.".$group, ($checkedInThisGroup->count() == count($groupedPermissions))))checked="checked"@endif
                            />
                            <label for="{{$inputBag}}[permissions_all][{{$group}}]" class="form-check-label">@lang('admin/permissions/permissions_connect.group_all') {{$group}}</label>
                        </div>
                    </div>
                </div>
                <div class="card-body">
                    @foreach($groupedPermissions as $permission)
                        <div>
                            <div class="form-check form-check-inline">
                                <input type="checkbox"
                                       data-group="{{$group}}"
                                       name="{{$inputBag}}[permissions][{{$permission->id}}]"
                                       id="{{$inputBag}}[permissions][{{$permission->id}}]"
                                       value="1"
                                       class="form-check-input js_permission"
                                       @if(isset($rolesPermissionIds) && $rolesPermissionIds->contains($permission->id))disabled="disabled"@endif
                                       @if(old("{$inputBag}.permissions.".$permission->id, $checkedInThisGroup->contains($permission->id)))checked="checked"@endif
                                />
                                <label for="{{$inputBag}}[permissions][{{$permission->id}}]" class="form-check-label">{{$permission->name}}</label>
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>
        </div>
    @endforeach
</div>
