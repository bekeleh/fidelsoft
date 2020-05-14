<?php $__env->startSection('title', 'Error'); ?>

<?php $__env->startSection('message', 'Something went wrong. Check your account authorized to do such thing, or contact your IT admin, Maybe <a href="<?php echo e(url('/')); ?>">'); ?>

<?php echo $__env->make('errors::layout', array_except(get_defined_vars(), array('__data', '__path')))->render(); ?>