<!-- permission list -->
<style>
    .form-horizontal .control-label {
        padding-top: 0px;
    }

    input[type='text'][disabled], input[disabled], textarea[disabled], input[readonly], textarea[readonly], .form-control[disabled], .form-control[readonly], fieldset[disabled] .form-control {
        background-color: white;
        color: #555555;
        cursor: text;
    }

    table.permissions {
        display: flex;
        flex-direction: column;
    }

    .permissions.table > thead, .permissions.table > tbody {
        alignment: center;
        min-width: 60%;
        margin: 0px 15px 15px;
    }

    .permissions.table > tbody + tbody {

    }

    .header-row {
        border-bottom: 1px solid #ccc;
    }

    .header-row h3 {
        margin: 0px;
    }

    .permissions-row {
        display: flex;
        justify-content: space-between;
        align-items: flex-start;
    }

    .table > tbody > tr > td.permissions-item {
        padding: 1px 1px 1px 8px;
    }

    .header-name {
        cursor: pointer;
    }
</style>

<table class="table table-striped permissions">
    <thead>
    <tr class="permissions-row">
        <th class="col-md-5">Permission</th>
        <th class="col-md-1">Grant</th>
        <th class="col-md-1">Deny</th>
        <th class="col-md-1">Inherit</th>
    </tr>
    </thead>
    <tbody>
    @foreach ($permissions as $area => $permissionsArray)
        @if (count($permissionsArray) == 1)
            <?php $localPermission = $permissionsArray[0]; ?>
            <tr class="header-row permissions-row">
                <td class="col-md-5 tooltip-base permissions-item"
                    data-toggle="tooltip"
                    data-placement="right"
                    title="{{ $localPermission['note'] }}">
                    <h4>{{ $area . ': ' . $localPermission['label'] }}</h4>
                </td>

                <td class="col-md-1 permissions-item">
                    @if (($localPermission['permission'] == 'superuser') && (!Auth::user()->isSuperUser()))
                        {{ Form::radio('permission['.$localPermission['permission'].']', '1',$userPermissions[$localPermission['permission'] ] == '1',['disabled'=>"disabled", 'class'=>'minimal']) }}
                    @else
                        {{ Form::radio('permission['.$localPermission['permission'].']', '1',$userPermissions[$localPermission['permission'] ] == '1',['value'=>"grant", 'class'=>'minimal']) }}
                    @endif
                </td>
                <td class="col-md-1 permissions-item">
                    @if (($localPermission['permission'] == 'superuser') && (!Auth::user()->isSuperUser()))
                        {{ Form::radio('permission['.$localPermission['permission'].']', '-1',$userPermissions[$localPermission['permission'] ] == '-1',['disabled'=>"disabled", 'class'=>'minimal']) }}
                    @else
                        {{ Form::radio('permission['.$localPermission['permission'].']', '-1',$userPermissions[$localPermission['permission'] ] == '-1',['value'=>"deny", 'class'=>'minimal']) }}
                    @endif
                </td>
                <td class="col-md-1 permissions-item">
                    @if (($localPermission['permission'] == 'superuser') && (!Auth::user()->isSuperUser()))
                        {{ Form::radio('permission['.$localPermission['permission'].']','0',$userPermissions[$localPermission['permission'] ] == '0',['disabled'=>"disabled",'class'=>'minimal'] ) }}
                    @else
                        {{ Form::radio('permission['.$localPermission['permission'].']','0',$userPermissions[$localPermission['permission'] ] == '0',['value'=>"inherit", 'class'=>'minimal'] ) }}
                    @endif
                </td>
            </tr>
        @else
            <tr class="header-row permissions-row">
                <td class="col-md-5 header-name">
                    <h3>{{ $area }}</h3>
                </td>
                <td class="col-md-1 permissions-item">
                    {{ Form::radio("$area", '1',false,['value'=>"grant", 'class'=>'minimal', 'data-checker-group' => str_slug($area)]) }}
                </td>
                <td class="col-md-1 permissions-item">
                    {{ Form::radio("$area", '-1',false,['value'=>"deny", 'class'=>'minimal', 'data-checker-group' => str_slug($area)]) }}
                </td>
                <td class="col-md-1 permissions-item">
                    {{ Form::radio("$area", '0',false,['value'=>"inherit", 'class'=>'minimal', 'data-checker-group' => str_slug($area)] ) }}
                </td>
            </tr>
            @foreach ($permissionsArray as $index => $permission)
                <tr class="permissions-row">
                    @if ($permission['display'])
                        <td class="col-md-5 tooltip-base permissions-item" data-toggle="tooltip" data-placement="right"
                            title="{{ $permission['note'] }}">
                            {{ $permission['label'] }}
                        </td>
                        <td class="col-md-1 permissions-item">
                            @if (($permission['permission'] == 'superuser') && (!Auth::user()->isSuperUser()))
                                {{ Form::radio('permission['.$permission['permission'].']', '1', $userPermissions[$permission['permission'] ] == '1', ["value"=>"grant", 'disabled'=>'disabled', 'class'=>'minimal radiochecker-'.str_slug($area)]) }}
                            @else
                                {{ Form::radio('permission['.$permission['permission'].']', '1', $userPermissions[ $permission['permission'] ] == '1', ["value"=>"grant",'class'=>'minimal radiochecker-'.str_slug($area)]) }}
                            @endif
                        </td>
                        <td class="col-md-1 permissions-item">
                            @if (($permission['permission'] == 'superuser') && (!Auth::user()->isSuperUser()))
                                {{ Form::radio('permission['.$permission['permission'].']', '-1', $userPermissions[$permission['permission'] ] == '-1', ["value"=>"deny", 'disabled'=>'disabled', 'class'=>'minimal radiochecker-'.str_slug($area)]) }}
                            @else
                                {{--                                {{ Form::radio('permission['.$permission['permission'].']', '-1', $userPermissions[$permission['permission'] ] == '-1', ["value"=>"deny",'class'=>'minimal radiochecker-'.str_slug($area)]) }}--}}
                                {{ Form::radio('permission['.$permission['permission'].']', '-1', $userPermissions[$permission['permission'] ] == '-1', ["value"=>"deny", 'disabled'=>'disabled', 'class'=>'minimal radiochecker-'.str_slug($area)]) }}
                            @endif
                        </td>
                        <td class="col-md-1 permissions-item">
                            @if (($permission['permission'] == 'superuser') && (!Auth::user()->isSuperUser()))
                                {{ Form::radio('permission['.$permission['permission'].']', '0', $userPermissions[$permission['permission']] =='0', ["value"=>"inherit", 'disabled'=>'disabled', 'class'=>'minimal radiochecker-'.str_slug($area)]) }}
                            @else
                                {{ Form::radio('permission['.$permission['permission'].']', '0', $userPermissions[$permission['permission']] =='0', ["value"=>"inherit", 'class'=>'minimal radiochecker-'.str_slug($area)]) }}
                            @endif
                        </td>
                    @endif
                </tr>
            @endforeach
        @endif
    @endforeach
    </tbody>
</table><!-- /.permission -->
<script nonce="{{ csrf_token() }}">
    $(document).ready(function () {
// Check/Uncheck all radio buttons in the group
        $('tr.header-row input:radio').on('ifClicked', function () {
            alert('yes..');
            value = $(this).attr('value');
            area = $(this).data('checker-group');
            $('.radiochecker-' + area + '[value=' + value + ']').iCheck('check');
        });

        $('.header-name').click(function () {
            $(this).parent().nextUntil('tr.header-row').slideToggle(500);
        });

        $('.tooltip-base').tooltip({container: 'body'});
        $(".superuser").change(function () {
            var perms = $(this).val();
            if (perms == '1') {
                $("#nonadmin").hide();
            } else {
                $("#nonadmin").show();
            }
        });
    });
</script>