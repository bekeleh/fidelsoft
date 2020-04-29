<?php $__env->startSection('content'); ?>
    ##parent-placeholder-040f06fd774092478d450774f5ba30c5da78acc8##
    <?php echo $__env->make('accounts.nav', ['selected' => ACCOUNT_TAX_RATES], array_except(get_defined_vars(), array('__data', '__path')))->render(); ?>
    <?php echo Former::open($url)->method($method)
    ->rules([
    'name' => 'required',
    'rate' => 'required',
    'is_inclusive' => 'required',
    ])
    ->addClass('warn-on-exit'); ?>

    <div class="panel panel-default">
        <div class="panel-heading">
            <h3 class="panel-title"><?php echo $title; ?></h3>
        </div>
        <div class="row">
            <div class="col-lg-10 col-lg-offset-1">
                <div class="panel panel-default">
                    <div class="panel-body form-padding-right">
                        <?php if($taxRate): ?>
                            <?php echo e(Former::populate($taxRate)); ?>

                            <?php echo e(Former::populateField('is_inclusive', intval($taxRate->is_inclusive))); ?>

                        <?php endif; ?>
                        <?php echo Former::text('name')->label('texts.tax_rate_name'); ?>

                        <?php echo Former::text('rate')->label('texts.rate')->append('%'); ?>


                        <?php if(!$taxRate && ! auth()->user()->account->inclusive_taxes): ?>
                            <?php echo Former::radios('is_inclusive')->radios([
                            trans('texts.exclusive') . ': 100 + 10% = 100 + 10' => array('name' => 'is_inclusive', 'value' => 0),
                            trans('texts.inclusive') . ':&nbsp; 100 + 10% = 90.91 + 9.09' => array('name' => 'is_inclusive', 'value' => 1),
                            ])->check(0)
                            ->label('type')
                            ->help('tax_rate_type_help'); ?>

                        <?php endif; ?>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <center class="buttons">
        <?php echo Button::normal(trans('texts.cancel'))->large()->asLinkTo(URL::to('/settings/tax_rates'))->appendIcon(Icon::create('remove-circle')); ?>

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