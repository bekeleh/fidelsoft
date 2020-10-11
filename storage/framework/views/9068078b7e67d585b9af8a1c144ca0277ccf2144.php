<?php $__env->startSection('content'); ?>
  ##parent-placeholder-040f06fd774092478d450774f5ba30c5da78acc8##
  <?php echo $__env->make('accounts.nav', ['selected' => ACCOUNT_API_TOKENS], array_except(get_defined_vars(), array('__data', '__path')))->render(); ?>

  <?php echo Former::open($url)->method($method)->addClass('warn-on-exit')->rules(array(
      'name' => 'required',
  ));; ?>


  <div class="panel panel-default">
      <div class="panel-heading" style="color:white;background-color: #777 !important;">
          <h3 class="panel-title in-bold-white"><?php echo trans($title); ?></h3>
</div>
<div class="panel-body form-padding-right">

  <?php if($token): ?>
    <?php echo Former::populate($token); ?>

  <?php endif; ?>

  <?php echo Former::text('name'); ?>


</div>
</div>

    <?php if(Auth::user()->hasFeature(FEATURE_API)): ?>
      <center class="buttons">
        <?php echo Button::normal(trans('texts.cancel'))->asLinkTo(URL::to('/settings/api_tokens'))->appendIcon(Icon::create('remove-circle'))->large(); ?>

        <?php echo Button::success(trans('texts.save'))->submit()->large()->appendIcon(Icon::create('floppy-disk')); ?>

      </center>
    <?php else: ?>
        <script>
            $(function() {
                $('form.warn-on-exit input').prop('disabled', true);
            });
        </script>
    <?php endif; ?>

  <?php echo Former::close(); ?>


<?php $__env->stopSection(); ?>

<?php $__env->startSection('onReady'); ?>
    $('#name').focus();
<?php $__env->stopSection(); ?>

<?php echo $__env->make('header', array_except(get_defined_vars(), array('__data', '__path')))->render(); ?>