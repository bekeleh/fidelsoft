<?php $__env->startSection('content'); ?>
    ##parent-placeholder-040f06fd774092478d450774f5ba30c5da78acc8##
    <?php echo Former::open($url)
    ->method($method)
    ->autocomplete('off')
    ->rules(['name' => 'required|max:255','rate' => 'required','notes' => 'required' ])
    ->addClass('col-lg-10 col-lg-offset-1 main-form warn-on-exit'); ?>

    <?php if($taxRate): ?>
        <?php echo e(Former::populate($taxRate)); ?>

        <?php echo e(Former::populateField('is_inclusive', intval($taxRate->is_inclusive))); ?>

        <div style="display:none">
            <?php echo Former::text('public_id'); ?>

        </div>
    <?php endif; ?>
    <span style="display:none">
        <?php echo Former::text('public_id'); ?>

        <?php echo Former::text('action'); ?>

        </span>
    <div class="row">
        <div class="col-lg-10 col-lg-offset-1">
            <div class="panel panel-default">
                <div class="panel-body form-padding-right">
                    <?php echo Former::text('name')->label('texts.tax_rate_name'); ?>

                    <?php echo Former::text('rate')->label('texts.rate')->append('%'); ?>

                    <?php echo Former::radios('is_inclusive')->radios([
                    trans('texts.exclusive') . ': 100 + 10% = 100 + 10' => array('name' => 'is_inclusive', 'value' => 0),
                    trans('texts.inclusive') . ':&nbsp; 100 + 10% = 90.91 + 9.09' => array('name' => 'is_inclusive', 'value' => 1),
                    ])->check(0)
                    ->label('type')
                    ->help('tax_rate_type_help'); ?>

                    <?php echo Former::textarea('notes')->rows(6); ?>

                </div>
            </div>
        </div>
    </div>
    <?php if(Auth::user()->canCreateOrEdit(ENTITY_TAX_RATE, $taxRate)): ?>
        <center class="buttons">
            <?php echo Button::normal(trans('texts.cancel'))->large()->asLinkTo(HTMLUtils::previousUrl('/tax_rates'))->appendIcon(Icon::create('remove-circle')); ?>

            <?php echo Button::success(trans('texts.save'))->submit()->large()->appendIcon(Icon::create('floppy-disk')); ?>

            <?php if($taxRate): ?>
                <?php echo DropdownButton::normal(trans('texts.more_actions'))
                ->withContents($taxRate->present()->moreActions())
                ->large()
                ->dropup(); ?>

            <?php endif; ?>
        </center>
    <?php endif; ?>
    <?php echo Former::close(); ?>

    <script type="text/javascript">
        $(function () {
            $('#name').focus();
        });

        function submitAction(action) {
            $('#action').val(action);
            $('.main-form').submit();
        }

        function onDeleteClick() {
            sweetConfirm(function () {
                submitAction('delete');
            });
        }
    </script>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('header', array_except(get_defined_vars(), array('__data', '__path')))->render(); ?>