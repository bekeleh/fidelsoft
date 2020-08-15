<?php $__env->startSection('content'); ?>

<p>&nbsp;<p>
    <p>&nbsp;<p>

        <div class="well">
            <div class="container" style="min-height:400px">
                <h3><?php echo e(trans('texts.error_title')); ?>...</h3>
                <h4><?php echo e($error); ?></h4>
                <h4><?php echo e(trans('texts.error_contact_text', ['mailaddress' => env('CONTACT_EMAIL', env('MAIL_FROM_ADDRESS'))])); ?></h4>
            </div>
        </div>

        <p>&nbsp;<p>
            <p>&nbsp;<p>

                <?php $__env->stopSection(); ?>

<?php echo $__env->make('public.header', array_except(get_defined_vars(), array('__data', '__path')))->render(); ?>