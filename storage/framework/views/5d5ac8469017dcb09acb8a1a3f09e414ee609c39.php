<?php $__env->startSection('markup'); ?>
<?php if ($account->emailMarkupEnabled()): ?>
    <?php echo $__env->make('emails.partials.client_view_action', array_except(get_defined_vars(), array('__data', '__path')))->render(); ?>
<?php endif; ?>
<?php $__env->stopSection(); ?>

<?php $__env->startSection('content'); ?>
    <tr>
        <td bgcolor="#F4F5F5" style="border-collapse: collapse;">&nbsp;</td>
    </tr>
    <tr>
        <td style="border-collapse: collapse;">
            <table cellpadding="10" cellspacing="0" border="0"
                   bgcolor="<?php echo e($account->primary_color ?: '#2E2B2B'); ?>" width="600" align="center"
                   class="header"
                   style="border-bottom-width: 6px; border-bottom-color: <?php echo e($account->primary_color ?: '#2E2B2B'); ?>; border-bottom-style: solid;">
                <tr>
                    <td class="logo" width="205"
                        style="border-collapse: collapse; vertical-align: middle; line-height: 16px;" valign="middle">
                        <?php echo $__env->make('emails.partials.account_logo', array_except(get_defined_vars(), array('__data', '__path')))->render(); ?>
                    </td>
                    <td width="183" style="border-collapse: collapse; vertical-align: middle; line-height: 16px;"
                        valign="middle">
                        <p class="left" style="line-height: 22px; margin: 3px 0 0; padding: 0;">
                            <?php if ($invoice->due_date): ?>
                                <span style="font-size: 11px; color: #8f8d8e;">
                                    <?php if ($invoice->isQuote()): ?>
                                        <?php echo e(strtoupper(trans('texts.valid_until'))); ?><?php echo e($account->formatDate($invoice->due_date)); ?>

                                    <?php else: ?>
                                        <?php if ($account->hasCustomLabel('due_date')): ?>
                                            <?php echo e($account->getLabel('due_date')); ?><?php echo e($account->formatDate($invoice->partial_due_date ?: $invoice->due_date)); ?>

                                        <?php else: ?>
                                            <?php echo e(utrans('texts.due_by', ['date' => $account->formatDate($invoice->partial_due_date ?: $invoice->due_date)])); ?>

                                        <?php endif; ?>
                                    <?php endif; ?>
                                </span><br/>
                            <?php endif; ?>
                            <span style="font-size: 19px; color: #FFFFFF;">
                                <?php echo e(trans("texts.{$entityType}")); ?><?php echo e($invoice->invoice_number); ?>

                            </span>
                        </p>
                    </td>
                    <td style="border-collapse: collapse; vertical-align: middle; line-height: 16px;" valign="middle">
                        <p style="margin: 0; padding: 0;">
                            <?php if (!isset($isRefund) || !$isRefund): ?>
                                <span style="font-size: 12px; color: #8f8d8e;">
                                    <?php echo e(strtoupper(trans('texts.' . $invoice->present()->balanceDueLabel))); ?>:
                                </span><br/>
                                <span class="total"
                                      style="font-size: 27px; color: #FFFFFF; margin-top: 5px;display: block;">
                                    <?php echo e($account->formatMoney($invoice->getRequestedAmount(), $client)); ?>

                                </span>
                            <?php endif; ?>
                        </p>
                    </td>
                </tr>
            </table>
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
        <?php if (!$account->isPaid()): ?>
            <?php echo trans('texts.ninja_email_footer', ['site' => link_to(NINJA_WEB_URL . '?utm_source=email_footer', APP_NAME)]); ?>

        <?php else: ?>
            <?php echo e($account->present()->address); ?>

            <br/>
            <?php if ($account->website): ?>
                <strong><a href="<?php echo e($account->present()->website); ?>"
                           style="color: #A7A6A6; text-decoration: none; font-weight: bold; font-size: 10px;"><?php echo e($account->website); ?></a></strong>
            <?php endif; ?>
        <?php endif; ?>
    </p>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('emails.master', array_except(get_defined_vars(), array('__data', '__path')))->render(); ?>