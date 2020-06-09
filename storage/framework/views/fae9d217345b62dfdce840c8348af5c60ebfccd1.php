<?php $__env->startSection('head'); ?>
<?php if (!empty($clientauth) && $fontsUrl = Utils::getAccountFontsUrl()): ?>
    <link href="<?php echo e($fontsUrl); ?>" rel="stylesheet" type="text/css">
<?php endif; ?>
    <link href="<?php echo e(asset('css/built.public.css')); ?>?no_cache=<?php echo e(NINJA_VERSION); ?>"
          rel="stylesheet" type="text/css"/>
    <link href="<?php echo e(asset('css/bootstrap.min.css')); ?>?no_cache=<?php echo e(NINJA_VERSION); ?>"
          rel="stylesheet" type="text/css"/>
    <link href="<?php echo e(asset('css/built.css')); ?>?no_cache=<?php echo e(NINJA_VERSION); ?>" rel="stylesheet"
          type="text/css"/>
    <link href="<?php echo e(asset('css/built.login.css')); ?>?no_cache=<?php echo e(NINJA_VERSION); ?>"
          rel="stylesheet" type="text/css"/>

<?php if (!empty($clientauth)): ?>
    <style type="text/css"><?php echo Utils::clientViewCSS(); ?></style>
<?php endif; ?>
<?php $__env->stopSection(); ?>

<?php $__env->startSection('body'); ?>

<?php echo $__env->yieldContent('form'); ?>

<?php $__env->stopSection(); ?>

<?php echo $__env->make('master', array_except(get_defined_vars(), array('__data', '__path')))->render(); ?>