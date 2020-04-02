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
{!! Former::open('/users')->autocomplete('off')->addClass('mainForm') !!}
<div style="display:none">
    {!! Former::text('action')->value('updatePermission')!!}
    {!! Former::text('public_id')->value($user->public_id) !!}
</div>
<div class="panel panel-default">
    <div class="panel-body">
        <div class="row">
            <div class="col-md-12">
                @if (!Auth::user()->isSuperUser())
                    <p class="alert alert-warning">
                        Only super admin may grant a user super admin access.
                    </p>
                @endif
            </div>
            <div>
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
            </div>
            <center class="buttons">
                {!! Button::normal(trans('texts.cancel'))->large()->asLinkTo(HTMLUtils::previousUrl('/users'))->appendIcon(Icon::create('remove-circle')) !!}
                {{--                {!! Button::success(trans('texts.save'))->submit()->large()->appendIcon(Icon::create('floppy-disk')) !!}--}}
                <button type="button" class="btn btn-success" onclick="submitChangePermission()"
                        id="changePermissionButton">
                    {{ trans('texts.save') }}
                    <i class="glyphicon glyphicon-floppy-disk"></i>
                </button>
            </center>
        </div>
    </div>
</div>
{!! Former::close() !!}

<script type="text/javascript">
    function submitChangePermission() {
        // var isChecked = $('tr.permissions-row input:radio:checked').iCheck('check');
        var isChecked = $('tr.permissions-row input:radio:checked').iCheck('check');
        for (var i = 0; i < 15; i++) {
            console.log(isChecked[i].name + '=>' + isChecked[i].value);
        }
    }
</script>