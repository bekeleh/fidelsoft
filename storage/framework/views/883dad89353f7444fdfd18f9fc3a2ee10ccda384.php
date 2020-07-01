













































































<?php echo $__env->make('partials.warn_session', ['redirectTo' => '/dashboard'], array_except(get_defined_vars(), array('__data', '__path')))->render(); ?>

<?php if(Session::has('warning')): ?>
    <div class="alert alert-warning"><?php echo Session::get('warning'); ?></div>
<?php elseif(env('WARNING_MESSAGE')): ?>
    <div class="alert alert-warning"><?php echo env('WARNING_MESSAGE'); ?></div>
<?php endif; ?>

<?php if(Session::has('message')): ?>
    <div class="alert alert-info alert-hide" style="z-index:9999">
        <?php echo e(Session::get('message')); ?>

    </div>
<?php elseif(Session::has('news_feed_message')): ?>
    <div class="alert alert-info">
        <?php echo Session::get('news_feed_message'); ?>

        <a href="#" onclick="hideMessage()" class="pull-right"><?php echo e(trans('texts.hide')); ?></a>
    </div>
<?php endif; ?>

<?php if(Session::has('error')): ?>
    <div class="alert alert-danger"><?php echo Session::get('error'); ?></div>
<?php endif; ?>

