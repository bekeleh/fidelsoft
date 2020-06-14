<?php $__env->startSection('content'); ?>
    ##parent-placeholder-040f06fd774092478d450774f5ba30c5da78acc8##

    <?php echo $__env->make('accounts.nav', ['selected' => ACCOUNT_PAYMENT_TERMS], array_except(get_defined_vars(), array('__data', '__path')))->render(); ?>

    <?php echo Former::open($url)->method($method)
        ->rules([
          'num_days' => 'required'
         ])
        ->addClass('warn-on-exit'); ?>


    <div class="panel panel-default">
        <div class="panel-heading" style="color:white;background-color: #777 !important;">
            <h3 class="panel-title in-bold-white">
                <?php echo $title; ?>

            </h3>
        </div>
        <div class="panel-body form-padding-right">
            <?php if($paymentTerm): ?>
                <?php echo e(Former::populate($paymentTerm)); ?>

            <?php endif; ?>
            <?php echo Former::text('num_days')->type('number')->min(1)->label('texts.num_days'); ?>

        </div>
    </div>
    <center class="buttons">
        <?php echo Button::normal(trans('texts.cancel'))->large()->asLinkTo(URL::to('/settings/payment_terms'))->appendIcon(Icon::create('remove-circle')); ?>

        <?php echo Button::success(trans('texts.save'))->submit()->large()->appendIcon(Icon::create('floppy-disk')); ?>

    </center>
    <?php echo Former::close(); ?>


    <script type="text/javascript">

        $(function () {
            $('#name').focus();
        });

    </script>

<?php $__env->stopSection(); ?>

<?php echo $__env->make('header', array_except(get_defined_vars(), array('__data', '__path')))->render(); ?>