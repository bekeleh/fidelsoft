<?php $__env->startSection('content'); ?>
    <?php echo Former::open($url)
        ->addClass('col-lg-10 col-lg-offset-1 warn-on-exit')
        ->method($method)
        ->rules([
        'vendor_id' => 'required',
        'amount' => 'required',
        'public_notes' => 'required',
        'private_notes' => 'required',
        'credit_date' => 'required',
        ]); ?>

    <?php if($credit): ?>
        <?php echo Former::populate($credit); ?>

        <div style="display:none">
            <?php echo Former::text('public_id'); ?>

        </div>
    <?php endif; ?>
    <div class="row">
        <div class="col-lg-10 col-lg-offset-1">

            <div class="panel panel-default">
                <div class="panel-body">

                    <?php if($credit): ?>
                        <?php echo Former::plaintext()->label('vendor')->value($vendor->present()->link); ?>

                    <?php else: ?>
                        <?php echo Former::select('vendor_id')
                        ->label('vendor')
                        ->addOption('', '')
                        ->addGroupClass('vendor-select'); ?>

                    <?php endif; ?>
                    <?php echo Former::text('amount'); ?>

                    <?php if($credit): ?>
                        <?php echo Former::text('balance'); ?>

                    <?php endif; ?>
                    <?php echo Former::text('credit_date')
                    ->data_date_format(Session::get(SESSION_DATE_PICKER_FORMAT, DEFAULT_DATE_PICKER_FORMAT))
                    ->addGroupClass('credit_date')
                    ->append('<i class="glyphicon glyphicon-calendar"></i>'); ?>

                    <?php echo Former::textarea('public_notes')->rows(4); ?>

                    <?php echo Former::textarea('private_notes')->rows(4); ?>

                </div>
            </div>

        </div>
    </div>

    <?php if(Auth::user()->canCreateOrEdit(ENTITY_VENDOR_CREDIT, $credit)): ?>
        <center class="buttons">
            <?php echo Button::normal(trans('texts.cancel'))->large()->asLinkTo(HTMLUtils::previousUrl('/vendor_credits'))->appendIcon(Icon::create('remove-circle')); ?>

            <?php echo Button::success(trans('texts.save'))->submit()->large()->appendIcon(Icon::create('floppy-disk')); ?>

        </center>
    <?php endif; ?>

    <?php echo Former::close(); ?>


    <script type="text/javascript">
        var vendors = <?php echo $vendors ?: 'false'; ?>;

        $(function () {
                    <?php if( ! $credit): ?>
            var $vendorSelect = $('select#vendor_id');
            for (var i = 0; i < vendors.length; i++) {
                var vendor = vendors[i];
                // var vendorName = getVendorDisplayName(vendor);
                // if (!vendorName) {
                //     continue;
                // }
                $vendorSelect.append(new Option(vendor.name, vendor.public_id));
            }

            if (<?php echo e($vendorPublicId ? 'true' : 'false'); ?>) {
                $vendorSelect.val(<?php echo e($vendorPublicId); ?>);
            }

            $vendorSelect.combobox({highlighter: comboboxHighlighter});
            <?php endif; ?>

            $('#currency_id').combobox();
            $('#credit_date').datepicker('update', '<?php echo e($credit ? $credit->credit_date : 'new Date()'); ?>');

            <?php if(!$vendorPublicId): ?>
            $('.vendor-select input.form-control').focus();
            <?php else: ?>
            $('#amount').focus();
            <?php endif; ?>

            $('.credit_date .input-group-addon').click(function () {
                toggleDatePicker('credit_date');
            });
        });

    </script>

<?php $__env->stopSection(); ?>

<?php echo $__env->make('header', array_except(get_defined_vars(), array('__data', '__path')))->render(); ?>