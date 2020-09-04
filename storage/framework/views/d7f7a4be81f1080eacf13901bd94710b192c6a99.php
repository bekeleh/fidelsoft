<?php $__env->startSection('markup'); ?>
    <?php if($account->emailMarkupEnabled()): ?>
        <?php echo $__env->make('emails.partials.client_view_action', array_except(get_defined_vars(), array('__data', '__path')))->render(); ?>
    <?php endif; ?>
<?php $__env->stopSection(); ?>

<?php $__env->startSection('content'); ?>
    <tr>
        <td bgcolor="#F4F5F5" style="border-collapse: collapse;">&nbsp;</td>
    </tr>
    <tr>
        <td style="border-collapse: collapse;">
            <?php if($entityType == ENTITY_INVOICE): ?>
                <?php echo $__env->make('emails.partials.invoice_body', array_except(get_defined_vars(), array('__data', '__path')))->render(); ?>
            <?php else: ?>
                <?php echo $__env->make('emails.partials.bill_body', array_except(get_defined_vars(), array('__data', '__path')))->render(); ?>
            <?php endif; ?>
        </td>
    </tr>
    <tr>
        <td class="content" style="border-collapse: collapse;">
            <div style="font-size: 18px; margin: 42px 40px 42px; padding: 0; max-width: 520px;"><?php echo $body; ?></div>
        </td>
    </tr>
<?php $__env->stopSection(); ?>

<?php $__env->startSection('footer'); ?>
    <p style="color: #A7A6A6; font-size: 13px; line-height: 18px; margin: 0 0 7px; padding: 0;">
        <?php if(! $account->isPaid()): ?>
            <?php echo trans('texts.ninja_email_footer', ['site' => link_to(NINJA_WEB_URL . '?utm_source=email_footer', APP_NAME)]); ?>

        <?php else: ?>
            <?php echo e($account->present()->address); ?>

            <br/>
            <?php if($account->website): ?>
                <strong>
                    <a href="<?php echo e($account->present()->website); ?>"
                       style="color: #A7A6A6; text-decoration: none; font-weight: bold; font-size: 10px;"><?php echo e($account->website); ?>

                    </a>
                </strong>
            <?php endif; ?>
        <?php endif; ?>
    </p>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('emails.master', array_except(get_defined_vars(), array('__data', '__path')))->render(); ?>