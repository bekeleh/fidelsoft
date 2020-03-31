<!-- permission list -->
<!-- Select2 -->
<link rel="stylesheet" href="{{ asset('js/plugins/select2/select2.min.css') }}">
<script src="{{ asset('js/plugins/iCheck/icheck.js') }}"></script>
<!-- iCheck for checkboxes and radio inputs -->
<link rel="stylesheet" href="{{ asset('js/plugins/iCheck/all.css') }}">

<style>
    .form-horizontal .control-label {
        padding-top: 1px;
    }

    input[type='text'][disabled], input[disabled], textarea[disabled], input[readonly], textarea[readonly], .form-control[disabled], .form-control[readonly], fieldset[disabled] .form-control {
        background-color: white;
        color: #25a186;
        cursor: text;
    }

    table.permissions {
        width: 80%;
        alignment: center;
        margin: 1px auto;
        flex-direction: column;
    }

    .permissions.table > thead, .permissions.table > tbody {
        margin: 5px;
        margin-top: 1px;
    }

    .header-row {
        border-bottom: 1px solid #ccc;
    }

    .header-row h3 {
        margin: 0px;
    }

    .header-row h4 {
        color: #25a186;
        margin: 0px;
        border-bottom: 1px solid #ccc;
        background-color: whitesmoke;
    }

    .table > tbody > tr > td.permissions-item {
        padding: 1px;
        padding-left: 5px;
        border-bottom: 1px solid #c0c0c0;
    }

    .header-name {
        cursor: pointer;
    }
</style>
<table class="table permissions dataTable">
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
            <?php $localPermission = $permissions_array[0]; ?>
            <tr class="header-row permissions-row">
                <td class="col-md-5 tooltip-base permissions-item"
                    data-toggle="tooltip"
                    data-placement="right"
                    title="{{ $localPermission['note'] }}">
                    <h3 style="color: #25a186;">{{ $area . ': ' . $localPermission['label'] }}</h3>
                </td>
                <!-- permission column -->
                <td class="col-md-1 permissions-item">
                    @if (($localPermission['permission'] === 'superuser') && (!Auth::user()->isSuperUser()))
                        {{ Form::radio('permission['.$localPermission['permission'].']', '1',$userPermissions[$localPermission['permission'] ] == '1',['disabled'=>"disabled", 'class'=>'minimal']) }}
                    @else
                        {{ Form::radio('permission['.$localPermission['permission'].']', '1',$userPermissions[$localPermission['permission'] ] == '1',['value'=>"grant", 'class'=>'minimal']) }}
                    @endif
                </td>
                <!-- denied column -->
                <td class="col-md-1 permissions-item">
                    @if (($localPermission['permission'] == 'superuser') && (!Auth::user()->isSuperUser()))
                        {{ Form::radio('permission['.$localPermission['permission'].']', '-1',$userPermissions[$localPermission['permission'] ] == '-1',['disabled'=>"disabled", 'class'=>'minimal']) }}
                    @else
                        {{ Form::radio('permission['.$localPermission['permission'].']', '-1',$userPermissions[$localPermission['permission'] ] == '-1',['value'=>"deny", 'class'=>'minimal']) }}
                    @endif
                </td>
                <!-- inherit column -->
                <td class="col-md-1 permissions-item">
                    @if (($localPermission['permission'] == 'superuser') && (!Auth::user()->isSuperUser()))
                        {{ Form::radio('permission['.$localPermission['permission'].']','0',$userPermissions[$localPermission['permission'] ] == '0',['disabled'=>"disabled",'class'=>'minimal'] ) }}
                    @else
                        {{ Form::radio('permission['.$localPermission['permission'].']','0',$userPermissions[$localPermission['permission'] ] == '0',['value'=>"inherit", 'class'=>'minimal'] ) }}
                    @endif
                </td>
            </tr>
        @else
            <tr class="header-row permissions-row" style="border-bottom: 1px;">
                <td class="col-md-5 header-name">
                    <h4 style="color: #25a186;">{{ ucwords($area) }}</h4>
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
                <tr class="permissions-row" style="border-bottom: 1px;">
                    @if ($permission['display'])
                        <td class="col-md-5 tooltip-base permissions-item"
                            data-toggle="tooltip"
                            data-placement="right"
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
