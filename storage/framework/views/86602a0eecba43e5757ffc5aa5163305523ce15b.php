<?php echo e(Former::populateField('notify_sent', intval(Auth::user()->notify_sent))); ?>

<?php echo e(Former::populateField('notify_viewed', intval(Auth::user()->notify_viewed))); ?>

<?php echo e(Former::populateField('notify_paid', intval(Auth::user()->notify_paid))); ?>

<?php echo e(Former::populateField('notify_approved', intval(Auth::user()->notify_approved))); ?>

<?php echo e(Former::populateField('only_notify_owned', intval(Auth::user()->only_notify_owned))); ?>


<div class="panel panel-default">
    <div class="panel-heading" style="color:white;background-color: #777 !important;">
        <h3 class="panel-title in-bold-white"><?php echo trans('texts.invoice_email_notifications'); ?></h3>
    </div>
    <div class="panel-body">
        <?php echo Former::checkbox('notify_sent')->label('&nbsp;')->text(trans('texts.invoice_email_sent'))->value(1); ?>

        <?php echo Former::checkbox('notify_viewed')->label('&nbsp;')->text(trans('texts.invoice_email_viewed'))->value(1); ?>

        <?php echo Former::checkbox('notify_paid')->label('&nbsp;')->text(trans('texts.invoice_email_paid'))->value(1); ?>

        <?php echo Former::checkbox('notify_approved')->label('&nbsp;')->text(trans('texts.invoice_email_approved'))->value(1); ?>


        <?php if(Auth()->user()->account->hasMultipleUsers()): ?>
            <br/>
            <?php echo Former::radios('only_notify_owned')->radios([
                    trans('texts.all_invoices') => array('name' => 'only_notify_owned', 'value' => 0),
                    trans('texts.my_invoices') => array('name' => 'only_notify_owned', 'value' => 1),
                ])->inline()
                    ->label('send_notifications_for'); ?>

        <?php endif; ?>
    </div>
</div>
