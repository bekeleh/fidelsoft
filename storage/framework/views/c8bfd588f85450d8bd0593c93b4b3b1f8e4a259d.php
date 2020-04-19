<?php $__env->startSection('form'); ?>
    <div class="form-signin">
        <h2 class="form-signin-heading"><?php echo e(trans('texts.session_expired')); ?></h2>
        <hr class="green">
        <div><center><?php echo e(trans('texts.client_session_expired_message')); ?></center></div>
    </div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('client_login', array_except(get_defined_vars(), array('__data', '__path')))->render(); ?>