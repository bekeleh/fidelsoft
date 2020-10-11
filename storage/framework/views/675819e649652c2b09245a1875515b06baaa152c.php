<?php $__env->startSection('content'); ?>
    ##parent-placeholder-040f06fd774092478d450774f5ba30c5da78acc8##

    <?php echo $__env->make('accounts.nav', ['selected' => ACCOUNT_TAX_RATES], array_except(get_defined_vars(), array('__data', '__path')))->render(); ?>

    <?php echo Former::open()->addClass('warn-on-exit'); ?>

    <?php echo e(Former::populate($account)); ?>

    <?php echo e(Former::populateField('invoice_taxes', intval($account->invoice_taxes))); ?>

    <?php echo e(Former::populateField('invoice_item_taxes', intval($account->invoice_item_taxes))); ?>

    <?php echo e(Former::populateField('enable_second_tax_rate', intval($account->enable_second_tax_rate))); ?>

    <?php echo e(Former::populateField('include_item_taxes_inline', intval($account->include_item_taxes_inline))); ?>

    <?php echo e(Former::populateField('inclusive_taxes', intval($account->inclusive_taxes))); ?>



    <div class="panel panel-default">
        <div class="panel-heading" style="color:white; background-color:  #777 !important;">
            <h3 class="panel-title in-bold-white"><?php echo trans('texts.tax_settings'); ?></h3>
        </div>
        <div class="panel-body">

            <?php echo Former::checkbox('invoice_taxes')
                ->text(trans('texts.enable_invoice_tax'))
                ->label('&nbsp;')
                ->value(1); ?>


            <?php echo Former::checkbox('invoice_item_taxes')
                ->text(trans('texts.enable_line_item_tax'))
                ->label('&nbsp;')
                ->value(1); ?>


            <?php echo Former::checkbox('enable_second_tax_rate')
                ->text(trans('texts.enable_second_tax_rate'))
                ->label('&nbsp;')
                ->value(1); ?>


            <?php echo Former::checkbox('include_item_taxes_inline')
                ->text(trans('texts.include_item_taxes_inline'))
                ->label('&nbsp;')
                ->value(1); ?>


            <?php if(! $hasInclusiveTaxRates && $countInvoices == 0): ?>
                <br/>
                <br/>
                <?php echo Former::checkbox('inclusive_taxes')
                    ->text(trans('texts.inclusive_taxes_help'))
                    ->label('&nbsp;')
                    ->help('<b>' . strtoupper(trans('texts.important')) . ': '
                        . trans('texts.inclusive_taxes_notice') . '</b>')
                    ->value(1); ?>

            <?php elseif($countInvoices <= 10): ?>
                <?php echo Former::plaintext(' ')->help(
                        trans($account->inclusive_taxes ? 'texts.taxes_are_included_help' : 'texts.taxes_are_not_included_help') . '<br/>' .
                        trans('texts.change_requires_purge', ['link' => link_to(url('/settings/account_management'), trans('texts.purging'))])); ?>

            <?php else: ?>
                <?php echo Former::plaintext(' ')->help(
                        trans($account->inclusive_taxes ? 'texts.taxes_are_included_help' : 'texts.taxes_are_not_included_help')); ?>

            <?php endif; ?>
            &nbsp;
            <?php if($taxRates->count()): ?>
                <?php echo $__env->make('partials.tax_rates', ['taxRateLabel' => trans('texts.default_tax_rate_id')], array_except(get_defined_vars(), array('__data', '__path')))->render(); ?>
            <?php endif; ?>

            <?php echo Former::actions( Button::success(trans('texts.save'))->submit()->appendIcon(Icon::create('floppy-disk')) ); ?>

            <?php echo Former::close(); ?>

        </div>
    </div>
    <script>
        window.onDatatableReady = actionListHandler;
    </script>


    <script type="text/javascript">
        $(function () {
            <?php if($countInvoices > 0): ?>
            $('#inclusive_taxes').change(function () {
                swal("<?php echo e(trans('texts.inclusive_taxes_warning')); ?>");
            });
            <?php endif; ?>
        })
    </script>

<?php $__env->stopSection(); ?>

<?php echo $__env->make('header', array_except(get_defined_vars(), array('__data', '__path')))->render(); ?>