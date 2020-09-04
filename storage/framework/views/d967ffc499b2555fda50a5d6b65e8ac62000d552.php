<!-- bill body -->
<table cellpadding="10" cellspacing="0" border="0" bgcolor="<?php echo e($account->primary_color ?: '#2E2B2B'); ?>" width="600"
       align="center" class="header"
       style="border-bottom-width: 6px; border-bottom-color: <?php echo e($account->primary_color ?: '#2E2B2B'); ?>; border-bottom-style: solid;">
    <tr>
        <td class="logo" width="205" style="border-collapse: collapse; vertical-align: middle; line-height: 16px;"
            valign="middle">
            <?php echo $__env->make('emails.partials.account_logo', array_except(get_defined_vars(), array('__data', '__path')))->render(); ?>
        </td>
        <td width="183" style="border-collapse: collapse; vertical-align: middle; line-height: 16px;" valign="middle">
            <p class="left" style="line-height: 22px; margin: 3px 0 0; padding: 0;">
                <?php if($bill->due_date): ?>
                    <span style="font-size: 11px; color: #8f8d8e;">
                                    <?php if($bill->isQuote()): ?>
                            <?php echo e(strtoupper(trans('texts.valid_until'))); ?> <?php echo e($account->formatDate($bill->due_date)); ?>

                        <?php else: ?>
                            <?php if($account->hasCustomLabel('due_date')): ?>
                                <?php echo e($account->getLabel('due_date')); ?> <?php echo e($account->formatDate($bill->partial_due_date ?: $bill->due_date)); ?>

                            <?php else: ?>
                                <?php echo e(utrans('texts.due_by', ['date' => $account->formatDate($bill->partial_due_date ?: $bill->due_date)])); ?>

                            <?php endif; ?>
                        <?php endif; ?>
                                </span><br/>
                <?php endif; ?>
                <span style="font-size: 19px; color: #FFFFFF;">
                                <?php echo e(trans("texts.{$entityType}")); ?> <?php echo e($bill->bill_number); ?>

                </span>
            </p>
        </td>
        <td style="border-collapse: collapse; vertical-align: middle; line-height: 16px;" valign="middle">
            <p style="margin: 0; padding: 0;">
                <?php if(! isset($isRefund) || ! $isRefund): ?>
                    <span style="font-size: 12px; color: #8f8d8e;">
                                    <?php echo e(strtoupper(trans('texts.' . $bill->present()->balanceDueLabel))); ?>:
                                </span>
                    <br/>
                    <span class="total" style="font-size: 27px; color: #FFFFFF; margin-top: 5px;display: block;">
                                    <?php echo e($account->formatMoney($bill->getRequestedAmount(), $vendor)); ?>

                                </span>
                <?php endif; ?>
            </p>
        </td>
    </tr>
</table>
