@extends('header')

@section('content')
    @parent
    {!! Former::open($url)
    ->method($method)
    ->autocomplete('off')
    ->rules([])
    ->addClass('col-lg-10 col-lg-offset-1 main-form warn-on-exit') !!}
    @if ($user)
        {{ Former::populate($user) }}
        <div style="display:none">
            {!! Former::text('public_id') !!}
        </div>
    @endif
    <!-- user detail -->
    <div class="panel panel-default">
        <div class="panel-heading" style="background-color:#777 !important">
            <h3 class="panel-title in-bold-white"> {{ trans('texts.user_details') }}</h3>
        </div>
        <div class="panel-body">
            <div class="col-md-3">
                @if ($user)
                    <p><i class="fa fa-id-number"
                          style="width: 20px"></i>{{ trans('texts.id_number').': '.$user->id }}</p>
                @endif
                @if ($user->first_name)
                    <p><i class="fa fa-user-o"
                          style="width: 20px"></i>{{ trans('texts.first_name').': '. $user->present()->fullName }}
                    </p>
                @endif
                @if ($user->notes)
                    <p><i>{!! nl2br(e($user->notes)) !!}</i></p>
                @endif
                @if ($user->last_login)
                    <h3 style="margin-top:0px"><small>
                            {{ trans('texts.last_logged_in') }} {{ Utils::timestampToDateTimeString(strtotime($user->last_login)) }}
                        </small>
                    </h3>
                @endif
            </div>
            <div class="col-md-3">
                <h3>{{ trans('texts.address') }}</h3>
                <p>address details</p>
            </div>
            <div class="col-md-3">
                <h3>{{ trans('texts.contacts') }}</h3>
                @if ($user->email)
                    <i class="fa fa-envelope"
                       style="width: 20px"></i>{!! HTML::mailto($user->email, $user->email) !!}<br/>
                @endif
                @if ($user->phone)
                    <i class="fa fa-phone" style="width: 20px"></i>{{ $user->phone }}<br/>
                @endif
                <br/>
            </div>
        </div>
    </div>
    <div class="panel panel-default">
        <div class="panel-heading" style="background-color:#777 !important">
            <h3 class="panel-title in-bold-white"> {!! trans('texts.permissions') !!} </h3>
        </div>
        <div class="panel-body">
            <div>
                @if ( ! Utils::hasFeature(FEATURE_USER_PERMISSIONS))
                    <div class="alert alert-warning">{{ trans('texts.upgrade_for_permissions') }}</div>
                    <script type="text/javascript">
                        $(function () {
                            $('input[type=checkbox]').prop('disabled', true);
                        })
                    </script>
                @endif
                <div class="alert alert-warning">
                    {!! Former::checkbox('is_admin')->label('&nbsp;')->text(trans('texts.administrator'))->value(1)->help(trans('texts.administrator_help')) !!}
                </div>
            </div>
            <div>
                <table class="table table-striped dataTable">
                    <thead>
                    <th></th>
                    <th style="padding-bottom:0px">{!! Former::checkbox('create')
    ->text( trans('texts.create') )
    ->value('create_')
    ->label('&nbsp;')
    ->id('create_all') !!}</th>
                    <th style="padding-bottom:0px">{!! Former::checkbox('view')
    ->text( trans('texts.view') )
    ->value('view_')
    ->label('&nbsp;')
    ->id('view_all') !!}</th>
                    <th style="padding-bottom:0px">{!! Former::checkbox('edit')
    ->text( trans('texts.edit') )
    ->value('edit_')
    ->label('&nbsp;')
    ->id('edit_all') !!}</th>
                    </thead>
                    <tbody>
                    @foreach (json_decode(PERMISSION_ENTITIES,1) as $permissionEntity)
                        <?php
                        if (isset($user->permissions))
                            $permissions = json_decode($user->permissions, 1);
                        else
                            $permissions = [];
                        ?>
                        <tr>
                            <td>{{ ucfirst($permissionEntity) }}</td>

                            <td>{!! Former::checkbox('permissions[create_' . $permissionEntity . ']')
    ->label('&nbsp;')
    ->value('create_' . $permissionEntity . '')
    ->id('create_' . $permissionEntity . '')
    ->check(is_array($permissions) && in_array('create_' . $permissionEntity, $permissions, FALSE) ? true : false) !!}</td>
                            <td>{!! Former::checkbox('permissions[view_' . $permissionEntity . ']')
    ->label('&nbsp;')
    ->value('view_' . $permissionEntity . '')
    ->id('view_' . $permissionEntity . '')
    ->check(is_array($permissions) && in_array('view_' . $permissionEntity, $permissions, FALSE) ? true : false) !!}</td>
                            <td>{!! Former::checkbox('permissions[edit_' . $permissionEntity . ']')
    ->label('&nbsp;')
    ->value('edit_' . $permissionEntity . '')
    ->id('edit_' . $permissionEntity . '')
    ->check(is_array($permissions) && in_array('edit_' . $permissionEntity, $permissions, FALSE) ? true : false) !!}</td>
                        </tr>
                    @endforeach
                    <tr>
                        <td><input type="checkbox" id="view_contact" value="view_contact"
                                   name="permissions[view_contact]" style="display:none">
                            <input type="checkbox" id="edit_contact" value="edit_contact"
                                   name="permissions[edit_contact]" style="display:none">
                            <input type="checkbox" id="create_contact" value="create_contact"
                                   name="permissions[create_contact]" style="display:none"></td>
                    </tr>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
    <center class="buttons">
        {!! Button::normal(trans('texts.cancel'))->asLinkTo(URL::to('/users'))->appendIcon(Icon::create('remove-circle'))->large() !!}
        {!! ($user) ? Button::success(trans('texts.save'))->withAttributes(['onclick' => 'submitAction()'])->large()->appendIcon(Icon::create('floppy-disk')) : false !!}
        {!! (! $user || ! $user->confirmed) ? Button::info(trans($user ? 'texts.resend_invite' : 'texts.send_invite'))->withAttributes(['onclick' => 'submitAction("email")'])->large()->appendIcon(Icon::create('send')) : false !!}
    </center>
    {!! Former::close() !!}
    <script type="text/javascript">
        function submitAction() {
            var inputElements = document.querySelectorAll('input[type=checkbox]:checked');
            var permissions = getPermission(inputElements);

            var $isAdmin = document.getElementById('is_admin').checked ? 1 : 0;

            var $account_id ={{$user->account_id}};
            var $public_id ={{$user->public_id}};
            $.ajax({
                url: '{{ URL::to('/users/change_permission') }}',
                type: 'POST',
                dataType: 'json',
                data: 'permissions=' + permissions + '&account_id=' + $account_id + '&public_id=' + $public_id + '&is_admin=' + $isAdmin,
                success: function (result) {
                    if (result.success) {
                        swal("{{trans('texts.updated_user_permission')}}");
                    }
                }
            });

        }

        function getPermission(isChecked) {
            var columns = [];
            var rows = [];
            for (var i = 0; i < isChecked.length; i++) {
                columns[i] = isChecked[i].name;
                rows[i] = isChecked[i].value;
            }
            return mapToJson(columns, rows);
        }

        function mapToJson(columns, rows) {
            var result = rows.reduce(function (result, field, index) {
                result[columns[index]] = field;
                return result;
            }, {});

            return JSON.stringify(result);
        }
    </script>
@stop

@section('onReady')

    $('#first_name').focus();

    /*
    *
    * Iterate over all permission checkboxes and ensure VIEW/EDIT
    * combinations are enabled/disabled depending on VIEW state.
    *
    */

    $("input[type='checkbox'][id^='view_']").each(function() {

    var entity = $(this).attr('id')
    .replace('create_',"")
    .replace('view_',"")
    .replace('edit_',"")
    .replace(']',"")
    .replace('[',""); //get entity name

    setCheckboxEditValue(entity);
    setContactPermission();

    });


    /*
    *
    * Checks state of View/Edit checkbox, will enable/disable check/uncheck
    * dependent on state of VIEW permission.
    *
    */

    $("input[type='checkbox'][id^='view_']").change(function(){

    var entity = $(this).attr('id')
    .replace('create_',"")
    .replace('view_',"")
    .replace('edit_',"")
    .replace(']',"")
    .replace('[',""); //get entity name

    setCheckboxEditValue(entity);
    setContactPermission();

    });

    $('#edit_client, #view_client, #create_client,#edit_vendor, #view_vendor, #create_vendor,#edit_permission, #view_permission, #create_permission').change(function() {
    switch($(this).val()) {

    case 'create_client':
    $('#create_contact').prop('disabled', false); //set state of edit checkbox
    $('#create_contact').prop('checked', $('#create_client').is(':checked') );
    break;

    case 'view_client':
    $('#view_contact').prop('disabled', false); //set state of edit checkbox
    $('#view_contact').prop('checked', $('#view_client').is(':checked') );
    break;

    case 'edit_client':
    $('#edit_contact').prop('disabled', false); //set state of edit checkbox
    $('#edit_contact').prop('checked', $('#edit_client').is(':checked') );
    break;

    case 'create_vendor':
    $('#create_vendor_contact').prop('disabled', false); //set state of edit checkbox
    $('#create_vendor_contact').prop('checked', $('#create_vendor').is(':checked') );
    break;

    case 'view_vendor':
    $('#view_vendor_contact').prop('disabled', false); //set state of edit checkbox
    $('#view_vendor_contact').prop('checked', $('#view_vendor').is(':checked') );
    break;

    case 'edit_vendor':
    $('#edit_vendor_contact').prop('disabled', false); //set state of edit checkbox
    $('#edit_vendor_contact').prop('checked', $('#edit_vendor').is(':checked') );
    break;


    case 'create_permission':
    $('#create_permission_group').prop('disabled', false); //set state of edit checkbox
    $('#create_permission_group').prop('checked', $('#create_permission').is(':checked') );
    break;

    case 'view_permission':
    $('#view_permission_group').prop('disabled', false); //set state of edit checkbox
    $('#view_permission_group').prop('checked', $('#view_permission').is(':checked') );
    break;

    case 'edit_permission':
    $('#edit_permission_group').prop('disabled', false); //set state of edit checkbox
    $('#edit_permission_group').prop('checked', $('#edit_permission').is(':checked') );
    break;

    }

    });

    $('#create_all, #view_all, #edit_all').change(function(){

    var checked = $(this).is(':checked');
    var permission_type = $(this).val();

    $("input[type='checkbox'][id^=" + permission_type + "]").each(function() {

    var entity = $(this).attr('id')
    .replace('create_',"")
    .replace('view_',"")
    .replace('edit_',"")
    .replace(']',"")
    .replace('[',""); //get entity name

    $('#' + permission_type + entity).prop('checked', checked); //set state of edit checkbox

    setCheckboxEditValue(entity);
    setContactPermission();

    });

    });

    function setCheckboxEditValue(entity) {

    if(!$('#view_' + entity).is(':checked')) {
    $('#edit_' + entity).prop('checked', false); //remove checkbox value from edit dependant on View state.
    }

    $('#edit_' + entity).prop('disabled', !$('#view_' + entity).is(':checked')); //set state of edit checkbox

    }

    function setContactPermission() {

    $('#view_contact').prop('checked', $('#view_client').is(':checked') );
    $('#edit_contact').prop('checked', $('#edit_client').is(':checked') );
    $('#create_contact').prop('checked', $('#create_client').is(':checked') );

    }
@stop