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
{!! Former::open('/permission_groups')->autocomplete('off')->addClass('mainForm') !!}
<div style="display:none">
    {!! Former::text('action')->value('updatePermission')!!}
    {!! Former::text('public_id')->value($permissionGroup->public_id) !!}
</div>
<div class="panel panel-default">
    <div class="panel-body">
        <div class="row">
            <div class="col-md-12">
                @if (!Auth::user()->isSuperUser())
                    <p class="alert alert-warning">
                        Only admin member can access page.
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
                                    <strong style="color: #25a186;">{{ $area . ': ' . $localPermission['label'] }}</strong>
                                </td>
                                <!-- permission column -->
                                <td class="col-md-1 permissions-item">
                                    @if (($localPermission['permission'] === 'superuser') && (!Auth::user()->isSuperUser()))
                                        {{ Form::radio('permission['.$localPermission['permission'].']', '1',$groupPermissions[$localPermission['permission'] ] == '1',['disabled'=>"disabled", 'class'=>'minimal']) }}
                                    @else
                                        {{ Form::radio('permission['.$localPermission['permission'].']', '1',$groupPermissions[$localPermission['permission'] ] == '1',['value'=>"grant", 'class'=>'minimal']) }}
                                    @endif
                                </td>
                                <!-- denied column -->
                                <td class="col-md-1 permissions-item">
                                    @if (($localPermission['permission'] == 'superuser') && (!Auth::user()->isSuperUser()))
                                        {{ Form::radio('permission['.$localPermission['permission'].']', '-1',$groupPermissions[$localPermission['permission'] ] == '-1',['disabled'=>"disabled", 'class'=>'minimal']) }}
                                    @else
                                        {{ Form::radio('permission['.$localPermission['permission'].']', '-1',$groupPermissions[$localPermission['permission'] ] == '-1',['value'=>"deny", 'class'=>'minimal']) }}
                                    @endif
                                </td>
                                <!-- inherit column -->
                                <td class="col-md-1 permissions-item">
                                    @if (($localPermission['permission'] == 'superuser') && (!Auth::user()->isSuperUser()))
                                        {{ Form::radio('permission['.$localPermission['permission'].']','0',$groupPermissions[$localPermission['permission'] ] == '0',['disabled'=>"disabled",'class'=>'minimal'] ) }}
                                    @else
                                        {{ Form::radio('permission['.$localPermission['permission'].']','0',$groupPermissions[$localPermission['permission'] ] == '0',['value'=>"inherit", 'class'=>'minimal'] ) }}
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
                                                {{ Form::radio('permission['.$permission['permission'].']', '1', $groupPermissions[$permission['permission'] ] == '1', ["value"=>"grant", 'disabled'=>'disabled', 'class'=>'minimal radiochecker-'.str_slug($area)]) }}
                                            @else
                                                {{ Form::radio('permission['.$permission['permission'].']', '1', $groupPermissions[ $permission['permission'] ] == '1', ["value"=>"grant",'class'=>'minimal radiochecker-'.str_slug($area)]) }}
                                            @endif
                                        </td>
                                        <td class="col-md-1 permissions-item">
                                            @if (($permission['permission'] == 'superuser') && (!Auth::user()->isSuperUser()))
                                                {{ Form::radio('permission['.$permission['permission'].']', '-1', $groupPermissions[$permission['permission'] ] == '-1', ["value"=>"deny", 'disabled'=>'disabled', 'class'=>'minimal radiochecker-'.str_slug($area)]) }}
                                            @else
                                                {{ Form::radio('permission['.$permission['permission'].']', '-1', $groupPermissions[$permission['permission'] ] == '-1', ["value"=>"deny",'class'=>'minimal radiochecker-'.str_slug($area)]) }}
                                            @endif
                                        </td>
                                        <td class="col-md-1 permissions-item">
                                            @if (($permission['permission'] == 'superuser') && (!Auth::user()->isSuperUser()))
                                                {{ Form::radio('permission['.$permission['permission'].']', '0', $groupPermissions[$permission['permission']] =='0', ["value"=>"inherit", 'disabled'=>'disabled', 'class'=>'minimal radiochecker-'.str_slug($area)]) }}
                                            @else
                                                {{ Form::radio('permission['.$permission['permission'].']', '0', $groupPermissions[$permission['permission']] =='0', ["value"=>"inherit", 'class'=>'minimal radiochecker-'.str_slug($area)]) }}
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
                {!! Button::normal(trans('texts.cancel'))->large()->asLinkTo(HTMLUtils::previousUrl('/permission_groups'))->appendIcon(Icon::create('remove-circle')) !!}
                {{--                {!! Button::success(trans('texts.save'))->submit()->large()->appendIcon(Icon::create('floppy-disk')) !!}--}}
                <button type="button" class="btn btn-success" onclick="submitChangePermission()"
                        id="changePermissionButton">
                    {{ trans('texts.save') }}
                    <i class="glyphicon glyphicon-floppy-disk"></i>
                </button>
                <br/>
                <br/>
                <div class="alert alert-success" role="alert"
                     style="padding-right:20px;padding-left:20px; display:none"
                     id="successDiv">
                    <strong>{{ trans('texts.success') }}</strong> {{ trans('texts.updated_permission_group') }}
                </div>
            </center>
        </div>
    </div>
</div>
{!! Former::close() !!}
<script nonce="{{ csrf_token() }}">
    $(document).ready(function () {
        $('input').iCheck({
            checkboxClass: 'icheckbox_square',
            radioClass: 'iradio_square',
            // radioClass: 'iradio_flat-orange',
            increaseArea: '5%',
        });
        // Check/Uncheck all radio buttons in the group
        $('tr.header-row input:radio').on('ifChanged', function () {
            value = $(this).attr('value');
            area = $(this).data('checker-group');
            $('.radiochecker-' + area + '[value=' + value + ']').iCheck('check');
        });

        $('.header-name').click(function () {
            $(this).parent().nextUntil('tr.header-row').slideToggle(1);
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

    function submitChangePermission() {
        var $account_id ={{$permissionGroup->account_id}};
        var $public_id ={{$permissionGroup->public_id}};
        var isChecked = $('td.permissions-item input:radio:checked').iCheck('check');
        var permissionArray = getPermission(isChecked);
        // console.log(permissionArray);
        $.ajax({
            url: '{{ URL::to('/permission_groups/change_group') }}',
            type: 'POST',
            datatype: 'json',
            data: 'permission=' + permissionArray + '&account_id=' + $account_id + '&public_id=' + $public_id,
            success: function (result) {
                if (result.success) {
                    $('#successDiv').show();
                }
            },

        });
    }

    function getPermission(isChecked) {
        var columns = [];
        var rows = [];
        for (var i = 0; i < isChecked.length; i++) {
            str = isChecked[i].name;
            columns[i] = str.slice(11, str.length - 1);
            rows[i] = isChecked[i].value;
        }
        return mapToJson(columns, rows);
    }

    function mapToJson(columns, rows) {
        var result = rows.reduce(function (result, field, index) {
            result[columns[index]] = field;
            return result;
        }, {})

        return JSON.stringify(result);
    }
</script>