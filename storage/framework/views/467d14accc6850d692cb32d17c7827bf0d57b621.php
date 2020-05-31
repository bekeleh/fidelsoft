<?php $__env->startSection('content'); ?>
    ##parent-placeholder-040f06fd774092478d450774f5ba30c5da78acc8##
    <?php echo Former::open($url)
    ->method($method)
    ->autocomplete('off')
    ->rules([])
    ->addClass('col-lg-10 col-lg-offset-1 main-form warn-on-exit'); ?>

    <?php if($user): ?>
        <?php echo e(Former::populate($user)); ?>

        <div style="display:none">
            <?php echo Former::text('public_id'); ?>

        </div>
    <?php endif; ?>
    <!-- user detail -->
    <div class="panel panel-default">
        <div class="panel-heading" style="background-color:#777 !important">
            <h3 class="panel-title in-bold-white"> <?php echo e(trans('texts.user_details')); ?></h3>
        </div>
        <div class="panel-body">
            <div class="col-md-3">
                <?php if($user): ?>
                    <p><i class="fa fa-id-number"
                          style="width: 20px"></i><?php echo e(trans('texts.id_number').': '.$user->id); ?></p>
                <?php endif; ?>
                <?php if($user->first_name): ?>
                    <p><i class="fa fa-user-o"
                          style="width: 20px"></i><?php echo e(trans('texts.first_name').': '. $user->present()->fullName); ?>

                    </p>
                <?php endif; ?>
                <?php if($user->notes): ?>
                    <p><i><?php echo nl2br(e($user->notes)); ?></i></p>
                <?php endif; ?>
                <?php if($user->last_login): ?>
                    <h3 style="margin-top:0px"><small>
                            <?php echo e(trans('texts.last_logged_in')); ?> <?php echo e(Utils::timestampToDateTimeString(strtotime($user->last_login))); ?>

                        </small>
                    </h3>
                <?php endif; ?>
            </div>
            <div class="col-md-3">
                <h3><?php echo e(trans('texts.address')); ?></h3>
                <p>address details</p>
            </div>
            <div class="col-md-3">
                <h3><?php echo e(trans('texts.contacts')); ?></h3>
                <?php if($user->email): ?>
                    <i class="fa fa-envelope"
                       style="width: 20px"></i><?php echo HTML::mailto($user->email, $user->email); ?><br/>
                <?php endif; ?>
                <?php if($user->phone): ?>
                    <i class="fa fa-phone" style="width: 20px"></i><?php echo e($user->phone); ?><br/>
                <?php endif; ?>
                <br/>
            </div>
        </div>
    </div>
    <div class="panel panel-default">
        <div class="panel-heading" style="background-color:#777 !important">
            <h3 class="panel-title in-bold-white"> <?php echo trans('texts.permissions'); ?> </h3>
        </div>
        <div class="panel-body">
            <div>
                <?php if( ! Utils::hasFeature(FEATURE_USER_PERMISSIONS)): ?>
                    <div class="alert alert-warning"><?php echo e(trans('texts.upgrade_for_permissions')); ?></div>
                    <script type="text/javascript">
                        $(function () {
                            $('input[type=checkbox]').prop('disabled', true);
                        })
                    </script>
                <?php endif; ?>
                <div class="alert alert-warning">
                    <?php echo Former::checkbox('is_admin')->label('&nbsp;')->text(trans('texts.administrator'))->value(1)->help(trans('texts.administrator_help')); ?>

                </div>
            </div>
            <div>
                <table class="table table-striped dataTable">
                    <thead>
                    <th></th>
                    <th style="padding-bottom:0px"><?php echo Former::checkbox('create')
    ->text( trans('texts.create') )
    ->value('create_')
    ->label('&nbsp;')
    ->id('create_all'); ?></th>
                    <th style="padding-bottom:0px"><?php echo Former::checkbox('view')
    ->text( trans('texts.view') )
    ->value('view_')
    ->label('&nbsp;')
    ->id('view_all'); ?></th>
                    <th style="padding-bottom:0px"><?php echo Former::checkbox('edit')
    ->text( trans('texts.edit') )
    ->value('edit_')
    ->label('&nbsp;')
    ->id('edit_all'); ?></th>
                    </thead>
                    <tbody>
                    <?php $__currentLoopData = json_decode(PERMISSION_ENTITIES,1); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $permissionEntity): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                        <?php
                        if (isset($user->permissions))
                            $permissions = json_decode($user->permissions, 1);
                        else
                            $permissions = [];
                        ?>
                        <tr>
                            <td><?php echo e(ucfirst($permissionEntity)); ?></td>

                            <td><?php echo Former::checkbox('permissions[create_' . $permissionEntity . ']')
    ->label('&nbsp;')
    ->value('create_' . $permissionEntity . '')
    ->id('create_' . $permissionEntity . '')
    ->check(is_array($permissions) && in_array('create_' . $permissionEntity, $permissions, FALSE) ? true : false); ?></td>
                            <td><?php echo Former::checkbox('permissions[view_' . $permissionEntity . ']')
    ->label('&nbsp;')
    ->value('view_' . $permissionEntity . '')
    ->id('view_' . $permissionEntity . '')
    ->check(is_array($permissions) && in_array('view_' . $permissionEntity, $permissions, FALSE) ? true : false); ?></td>
                            <td><?php echo Former::checkbox('permissions[edit_' . $permissionEntity . ']')
    ->label('&nbsp;')
    ->value('edit_' . $permissionEntity . '')
    ->id('edit_' . $permissionEntity . '')
    ->check(is_array($permissions) && in_array('edit_' . $permissionEntity, $permissions, FALSE) ? true : false); ?></td>
                        </tr>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
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
        <?php echo Button::normal(trans('texts.cancel'))->asLinkTo(URL::to('/users'))->appendIcon(Icon::create('remove-circle'))->large(); ?>

        <?php echo ($user) ? Button::success(trans('texts.save'))->withAttributes(['onclick' => 'submitAction()'])->large()->appendIcon(Icon::create('floppy-disk')) : false; ?>

        <?php echo (! $user || ! $user->confirmed) ? Button::info(trans($user ? 'texts.resend_invite' : 'texts.send_invite'))->withAttributes(['onclick' => 'submitAction("email")'])->large()->appendIcon(Icon::create('send')) : false; ?>

    </center>
    <?php echo Former::close(); ?>

    <script type="text/javascript">
        function submitAction() {
            var inputElements = document.querySelectorAll('input[type=checkbox]:checked');
            var permissions = getPermission(inputElements);

            var $isAdmin = document.getElementById('is_admin').checked ? 1 : 0;

            var $account_id =<?php echo e($user->account_id); ?>;
            var $public_id =<?php echo e($user->public_id); ?>;
            $.ajax({
                url: '<?php echo e(URL::to('/users/change_permission')); ?>',
                type: 'POST',
                dataType: 'json',
                data: 'permissions=' + permissions + '&account_id=' + $account_id + '&public_id=' + $public_id + '&is_admin=' + $isAdmin,
                success: function (result) {
                    if (result.success) {
                        swal("<?php echo e(trans('texts.updated_user_permission')); ?>");
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
            }, {})

            return JSON.stringify(result);
        }
    </script>
<?php $__env->stopSection(); ?>

<?php $__env->startSection('onReady'); ?>

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

    $('#edit_client, #view_client, #create_client').change(function() {
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
<?php $__env->stopSection(); ?>
<?php echo $__env->make('header', array_except(get_defined_vars(), array('__data', '__path')))->render(); ?>