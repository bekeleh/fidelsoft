<?php $__env->startSection('content'); ?>
    ##parent-placeholder-040f06fd774092478d450774f5ba30c5da78acc8##
    <!-- Group detail -->
    <div class="panel panel-default">
        <div class="panel-heading" style="background-color:#777 !important">
            <h3 class="panel-title in-bold-white"> <?php echo trans('texts.group_details'); ?> </h3>
        </div>
        <div class="panel-body">
            <?php if ($userGroup): ?>
                <p><i class="fa fa-id-number"
                      style="width: 20px"></i><?php echo e(trans('texts.id_number') . ': ' . $userGroup->id); ?></p>
            <?php endif; ?>
            <?php if ($userGroup->name): ?>
                <p><?php echo e(trans('texts.permission_group_name') . ': '); ?>

                    <strong><?php echo e($userGroup->present()->displayName); ?></strong></p>
            <?php endif; ?>
            <?php if ($userGroup->notes): ?>
                <p><i><?php echo nl2br(e($userGroup->notes)); ?></i></p>
            <?php endif; ?>
        </div>
    </div>
    <div class="panel panel-default">
        <div class="panel-heading" style="background-color:#777 !important">
            <h3 class="panel-title in-bold-white"> <?php echo trans('texts.permissions'); ?> </h3>
        </div>
        <div class="panel-body">
            <div>
                <?php if (!Utils::hasFeature(FEATURE_USER_PERMISSIONS)): ?>
                    <div class="alert alert-warning"><?php echo e(trans('texts.upgrade_for_permissions')); ?></div>
                    <script type="text/javascript">
                        $(function () {
                            $('input[type=checkbox]').prop('disabled', true);
                        })
                    </script>
                <?php endif; ?>
            </div>
            <div>
                <table class="table table-striped dataTable">
                    <thead>
                    <th><?php echo trans('texts.permissions'); ?></th>
                    <th style="padding-bottom:0px"><?php echo Former::checkbox('create')
                            ->text(trans('texts.create'))
                            ->value('create_')
                            ->label('&nbsp;')
                            ->id('create_all'); ?></th>
                    <th style="padding-bottom:0px"><?php echo Former::checkbox('view')
                            ->text(trans('texts.view'))
                            ->value('view_')
                            ->label('&nbsp;')
                            ->id('view_all'); ?></th>
                    <th style="padding-bottom:0px"><?php echo Former::checkbox('edit')
                            ->text(trans('texts.edit'))
                            ->value('edit_')
                            ->label('&nbsp;')
                            ->id('edit_all'); ?></th>
                    </thead>
                    <tbody>
                    <?php $__currentLoopData = json_decode(PERMISSION_ENTITIES, 1);
                    $__env->addLoop($__currentLoopData);
                    foreach ($__currentLoopData as $permissionEntity): $__env->incrementLoopIndices();
                        $loop = $__env->getLastLoop(); ?>
                        <?php
                        if (isset($userGroup->permissions))
                            $permissions = json_decode($userGroup->permissions, 1);
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
                    <?php endforeach;
                    $__env->popLoop();
                    $loop = $__env->getLastLoop(); ?>
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
        <?php echo Button::normal(trans('texts.cancel'))->asLinkTo(URL::to('/permission_groups'))->appendIcon(Icon::create('remove-circle'))->large(); ?>

        <?php echo ($userGroup) ? Button::success(trans('texts.save'))->withAttributes(['onclick' => 'submitAction()'])->large()->appendIcon(Icon::create('floppy-disk')) : false; ?>

        <?php echo (!$userGroup || !$userGroup->confirmed) ? Button::info(trans($userGroup ? 'texts.resend_invite' : 'texts.send_invite'))->withAttributes(['onclick' => 'submitAction("email")'])->large()->appendIcon(Icon::create('send')) : false; ?>

    </center>
<?php echo Former::close(); ?>

    <script type="text/javascript">
        function submitAction() {
            var inputElements = document.querySelectorAll('input[type=checkbox]:checked');
            var permissions = getPermission(inputElements);

            var $account_id =<?php echo e($userGroup->account_id); ?>;
            var $public_id =<?php echo e($userGroup->public_id); ?>;
            $.ajax({
                url: '<?php echo e(URL::to('/permission_groups/change_permission')); ?>',
                type: 'POST',
                dataType: 'json',
                data: 'permissions=' + permissions + '&account_id=' + $account_id + '&public_id=' + $public_id,
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
            }, {});

            return JSON.stringify(result);
        }
    </script>
<?php $__env->stopSection(); ?>

<?php $__env->startSection('onReady'); ?>
    $('#name').focus();

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