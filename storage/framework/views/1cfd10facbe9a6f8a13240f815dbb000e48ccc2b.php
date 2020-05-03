<?php $__env->startSection('content'); ?>
    ##parent-placeholder-040f06fd774092478d450774f5ba30c5da78acc8##
    <?php echo Former::open()->addClass('warn-on-exit')->rules([
        'first_name' => 'required',
        'last_name' => 'required',
        'username' => 'required',
        'email' => 'required|email',
    ]); ?>


    <?php if($user): ?>
        <?php echo e(Former::populate($user)); ?>

        <div style="display:none">
            <?php echo Former::text('public_id'); ?>

        </div>
    <?php endif; ?>
    <span style="display:none">
    <?php echo Former::text('public_id'); ?>

        <?php echo Former::text('action'); ?>

    </span>
    <div class="row">
        <div class="col-md-12">
            <div class="panel panel-default">
                <div class="panel-heading" style="background-color:#777 !important">
                    <h3 class="panel-title in-bold-white"><?php echo trans('texts.user_details'); ?></h3>
                </div>
                <div class="panel-body form-padding-right">
                    <?php echo Former::text('first_name')->readonly(); ?>

                    <?php echo Former::text('last_name')->readonly(); ?>

                    <?php echo Former::text('username')->readonly(); ?>

                    <?php echo Former::text('email')->readonly(); ?>

                    <?php echo Former::text('phone')->readonly(); ?>

                    <br/>
                </div>
            </div>
        </div>
    </div>
    <?php if( ! Auth::user()->is_admin): ?>
        <?php echo $__env->make('accounts.partials.notifications', array_except(get_defined_vars(), array('__data', '__path')))->render(); ?>
    <?php endif; ?>
    <center class="buttons">
        <?php if(Auth::user()->confirmed): ?>
            <?php echo Button::primary(trans('texts.change_password'))
                    ->appendIcon(Icon::create('lock'))
                    ->large()->withAttributes(['onclick'=>'showChangePassword()']); ?>

            
            <?php echo Button::primary(trans('texts.resend_confirmation'))
                    ->appendIcon(Icon::create('send'))
                    ->asLinkTo(URL::to('/resend_confirmation'))->large(); ?>

        <?php endif; ?>
        
        
        
    </center>
    <div class="modal fade" id="passwordModal" tabindex="-1" role="dialog" aria-labelledby="passwordModalLabel"
         aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
                    <h4 class="modal-title" id="passwordModalLabel"><?php echo e(trans('texts.change_password')); ?></h4>
                </div>
                <div class="container" style="width: 100%; padding-bottom: 0px !important">
                    <div class="panel panel-default">
                        <div class="panel-body">
                            <div style="background-color: #fff" id="changePasswordDiv"
                                 onkeyup="validateChangePassword()" onclick="validateChangePassword()"
                                 onkeydown="checkForEnter(event)">
                                <?php echo Former::password('newer_password')->style('width:300px')->label(trans('texts.new_password')); ?>

                                <?php echo Former::password('confirm_password')->style('width:300px')->help('<span id="passwordStrength">&nbsp;</span>'); ?>

                                &nbsp;<br/>
                                <center>
                                    <div id="changePasswordError"></div>
                                </center>
                                <br/>
                            </div>
                            <div style="padding-left:40px;padding-right:40px;display:none;min-height:130px"
                                 id="working">
                                <h3><?php echo e(trans('texts.working')); ?>...</h3>
                                <div class="progress progress-striped active">
                                    <div class="progress-bar" role="progressbar" aria-valuenow="100" aria-valuemin="0"
                                         aria-valuemax="100" style="width: 100%"></div>
                                </div>
                            </div>
                            <div style="background-color: #fff; padding-right:20px;padding-left:20px; display:none"
                                 id="successDiv">
                                <br/>
                                <h3><?php echo e(trans('texts.success')); ?></h3>
                                <?php echo e(trans('texts.updated_password')); ?>

                                <br/>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer" id="changePasswordFooter">
                    <button type="button" class="btn btn-default" id="cancelChangePasswordButton" data-dismiss="modal">
                        <?php echo e(trans('texts.cancel')); ?>

                        <i class="glyphicon glyphicon-remove-circle"></i>
                    </button>
                    <button type="button" class="btn btn-success" onclick="submitChangePassword()"
                            id="changePasswordButton" disabled>
                        <?php echo e(trans('texts.save')); ?>

                        <i class="glyphicon glyphicon-floppy-disk"></i>
                    </button>
                </div>
            </div>
        </div>
    </div>
    <?php echo Former::close(); ?>

    <script type="text/javascript">
        $(function () {
            $('#passwordModal').on('hidden.bs.modal', function () {
                $(['newer_password', 'confirm_password']).each(function (i, field) {
                    var $input = $('form #' + field);
                    $input.val('');
                    $input.closest('div.form-group').removeClass('has-success');
                });
                $('#changePasswordButton').prop('disabled', true);
            })

            // $('#passwordModal').on('shown.bs.modal', function () {
            //     $('#current_password').focus();
            // })
        });

        function showChangePassword() {
            $('#passwordModal').modal('show');
        }

        function validateChangePassword(showError) {
            var isFormValid = true;
            $(['newer_password', 'confirm_password']).each(function (i, field) {
                var $input = $('form #' + field),
                    val = $.trim($input.val());
                var isValid = val;

                if (field) {
                    isValid = val.length >= 6;
                }

                if (isValid && field == 'confirm_password') {
                    isValid = val == $.trim($('#newer_password').val());
                }

                if (isValid) {
                    $input.closest('div.form-group').removeClass('has-error').addClass('has-success');
                } else {
                    isFormValid = false;
                    $input.closest('div.form-group').removeClass('has-success');
                    if (showError) {
                        $input.closest('div.form-group').addClass('has-error');
                    }
                }

                if (field == 'newer_password') {
                    var score = scorePassword(val);
                    if (isValid) {
                        isValid = score > 50;
                    }

                    showPasswordStrength(val, score);
                }
            });

            $('#changePasswordButton').prop('disabled', !isFormValid);

            return isFormValid;
        }

        function submitChangePassword() {
            if (!validateChangePassword(true)) {
                return;
            }
            $('#changePasswordDiv, #changePasswordFooter').hide();
            $('#working').show();

            $.ajax({
                type: 'POST',
                url: '<?php echo e(URL::to('/force_reset_password/force_reset_password')); ?>',
                data: 'new_password=' + encodeURIComponent($('form #newer_password').val()) +
                    '&confirm_password=' + encodeURIComponent($('form #confirm_password').val()) +
                    '&public_id=' + <?php echo e($user->public_id); ?>,
                success: function (result) {
                    if (result == 'success') {
                        NINJA.formIsChanged = false;
                        $('#changePasswordButton').hide();
                        $('#successDiv').show();
                        $('#cancelChangePasswordButton').html('<?php echo e(trans('texts.close')); ?>');
                    } else {
                        $('#changePasswordError').html(result);
                        $('#changePasswordDiv').show();
                    }
                    $('#changePasswordFooter').show();
                    $('#working').hide();
                }
            });
        }

        function disableSocialLogin() {
            sweetConfirm(function () {
                window.location = '<?php echo e(URL::to('/auth_unlink')); ?>';
            });
        }
    </script>
<?php $__env->stopSection(); ?>
<?php $__env->startSection('onReady'); ?>
    $('#first_name').focus();
<?php $__env->stopSection(); ?>

<?php echo $__env->make('header', array_except(get_defined_vars(), array('__data', '__path')))->render(); ?>