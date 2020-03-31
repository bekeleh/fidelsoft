<!-- permission list -->
<!-- Select2 -->
<link rel="stylesheet" href="{{ asset('js/plugins/select2/select2.min.css') }}">
<script src="{{ asset('js/plugins/iCheck/icheck.js') }}"></script>
<!-- iCheck for checkboxes and radio inputs -->
<link rel="stylesheet" href="{{ asset('js/plugins/iCheck/all.css') }}">

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
        margin: 15px;
        margin-top: 0px;
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
        padding: 1px;
        padding-left: 8px;
    }

    .header-name {
        cursor: pointer;
    }
</style>
<table>
    <thead>
    <tr class="permissions-row">
        <th class="col-md-5">Permission</th>
        <th class="col-md-1">Grant</th>
        <th class="col-md-1">Deny</th>
        <th class="col-md-1">Inherit</th>
    </tr>
    </thead>
    <tbody>
    @foreach ($permissions as $area => $permissions_array)
        @if (count($permissions_array) === 1)
            <?php $local_permission = $permissions_array[0]; ?>
            <tr class="header-row permissions-row">
                <td class="col-md-5 tooltip-base permissions-item"
                    data-toggle="tooltip"
                    data-placement="right"
                    title="{{ $local_permission['notes'] }}">
                    <h4 style="color: #3c8dbc;">{{ $area . ': ' . $local_permission['label'] }}</h4>
                </td>
                <!-- permission column -->
                <td class="col-md-1 permissions-item">
                    @if (($local_permission['permission'] === 'superuser') && (!Auth::user()->isSuperUser()))
                        {{ Form::radio('permission['.$local_permission['permission'].']', '1',$userPermissions[$local_permission['permission'] ] == '1',['disabled'=>"disabled", 'class'=>'minimal']) }}
                    @else
                        {{ Form::radio('permission['.$local_permission['permission'].']', '1',$userPermissions[$local_permission['permission'] ] == '1',['value'=>"grant", 'class'=>'minimal']) }}
                    @endif
                </td>
                <!-- denied column -->
                <td class="col-md-1 permissions-item">
                    @if (($local_permission['permission'] == 'superuser') && (!Auth::user()->isSuperUser()))
                        {{ Form::radio('permission['.$local_permission['permission'].']', '-1',$userPermissions[$local_permission['permission'] ] == '-1',['disabled'=>"disabled", 'class'=>'minimal']) }}
                    @else
                        {{ Form::radio('permission['.$local_permission['permission'].']', '-1',$userPermissions[$local_permission['permission'] ] == '-1',['value'=>"deny", 'class'=>'minimal']) }}
                    @endif
                </td>
                <!-- inherit column -->
                <td class="col-md-1 permissions-item">
                    @if (($local_permission['permission'] == 'superuser') && (!Auth::user()->isSuperUser()))
                        {{ Form::radio('permission['.$local_permission['permission'].']','0',$userPermissions[$local_permission['permission'] ] == '0',['disabled'=>"disabled",'class'=>'minimal'] ) }}
                    @else
                        {{ Form::radio('permission['.$local_permission['permission'].']','0',$userPermissions[$local_permission['permission'] ] == '0',['value'=>"inherit", 'class'=>'minimal'] ) }}
                    @endif
                </td>
            </tr>
        @else
            <tr class="header-row permissions-row" style="border: 1px;">
                <td class="col-md-5 header-name">
                    <h3 style="color: #3c8dbc;">{{ $area }}</h3>
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
            @foreach ($permissions_array as $index => $permission)
                <tr class="permissions-row" style="border: 1px;">
                    @if ($permission['display'])
                        <td
                                class="col-md-5 tooltip-base permissions-item"
                                data-toggle="tooltip"
                                data-placement="right"
                                title="{{ $permission['notes'] }}">
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
                                {{ Form::radio('permission['.$permission['permission'].']', '-1', $userPermissions[$permission['permission'] ] == '-1', ["value"=>"deny",'class'=>'minimal radiochecker-'.str_slug($area)]) }}
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
</table>
