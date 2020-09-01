<?php $__env->startSection('head'); ?>
    ##parent-placeholder-1a954628a960aaef81d7b2d4521929579f3541e6##

    <?php echo $__env->make('money_script', array_except(get_defined_vars(), array('__data', '__path')))->render(); ?>

    <style type="text/css">
        .input-group-addon {
            min-width: 40px;
        }
    </style>
<?php $__env->stopSection(); ?>

<?php $__env->startSection('content'); ?>
    <?php echo Former::open($url)
        ->addClass('col-lg-10 col-lg-offset-1 warn-on-exit main-form')
        ->onsubmit('return onFormSubmit(event)')
        ->method($method)
        ->autocomplete('off')
        ->rules(array(
        'vendor' => 'required',
        'bill' => 'required',
        'amount' => 'required',
        'payment_date' => 'required',
        'payment_type_id' => 'required',
        'payment_status_id' => 'required',
        'transaction_reference' => 'required',
        'public_notes' => 'required',
        'private_notes' => 'required',
        )); ?>

    <?php if($payment): ?>
        <?php echo Former::populate($payment); ?>

    <?php else: ?>
        <?php if($account->payment_type_id): ?>
            <?php echo Former::populateField('payment_type_id', $account->payment_type_id); ?>

        <?php endif; ?>
    <?php endif; ?>

    <span style="display:none">
        <?php echo Former::text('public_id'); ?>

        <?php echo Former::text('action'); ?>

    </span>
    <div class="row">
        <div class="col-lg-10 col-lg-offset-1">
            <div class="panel panel-default">
                <div class="panel-body">
                <?php if($payment): ?>
                    <?php echo Former::plaintext()->label('vendor')->value($payment->vendor->present()->link); ?>

                    <?php echo Former::plaintext()->label('bill')->value($payment->bill->present()->link); ?>

                    <?php echo Former::plaintext()->label('amount')->value($payment->present()->amount); ?>

                <?php else: ?>
                    <?php echo Former::select('vendor')->addOption('', '')->addGroupClass('vendor-select'); ?>

                    <?php echo Former::select('bill')->addOption('', '')->addGroupClass('bill-select'); ?>

                    <?php echo Former::text('amount')->append('<span data-bind="html: paymentCurrencyCode"></span>'); ?>


                    <?php if(isset($paymentTypeId) && $paymentTypeId): ?>
                        <?php echo Former::populateField('payment_type_id', $paymentTypeId); ?>

                    <?php endif; ?>
                <?php endif; ?>
                <!-- payment type -->
                <?php if(!$payment || !$payment->account_gateway_id): ?>
                    <?php echo Former::select('payment_type_id')
                    ->placeholder(trans('select_payment_type'))
                    ->addOption('', '')
                    ->fromQuery($paymentTypes, 'name', 'id')
                    ->addGroupClass('payment-type-select'); ?>

                <?php endif; ?>

                <!-- payment status -->
                <?php echo Former::select('payment_status_id')->addOption('','')
                ->placeholder(trans('select_payment_status'))
                ->fromQuery($paymentStatuses, 'name', 'id')
                ->label(trans('texts.payment_status')); ?>

                <!-- payment date -->
                <?php echo Former::text('payment_date')
                ->data_date_format(Session::get(SESSION_DATE_PICKER_FORMAT))
                ->addGroupClass('payment_date')
                ->append('<i class="glyphicon glyphicon-calendar"></i>'); ?>

                <!-- payment reference -->
                <?php echo Former::text('transaction_reference')->value(time().str_random(4)); ?>

                <!-- payment public notes -->
                <?php echo Former::textarea('public_notes'); ?>

                <!-- payment private notes -->
                <?php echo Former::textarea('private_notes'); ?>

                <!-- payment currency -->
                    <?php if(!$payment || ($payment && ! $payment->isExchanged())): ?>
                        <?php echo Former::checkbox('convert_currency')
                        ->text(trans('texts.convert_currency'))
                        ->data_bind('checked: convert_currency')
                        ->label(' ')
                        ->value(1); ?>

                    <?php endif; ?>

                    <div style="display:none" data-bind="visible: enableExchangeRate">
                        <br/>
                        <!-- currency -->
                    <?php echo Former::select('exchange_currency_id')->addOption('','')
                    ->label(trans('texts.currency'))
                    ->data_placeholder(Utils::getFromCache($account->getCurrencyId(), 'currencies')->name)
                    ->data_bind('combobox: exchange_currency_id, disable: true')
                    ->fromQuery($currencies, 'name', 'id'); ?>

                    <!-- exchange rate -->
                        <?php echo Former::text('exchange_rate')
                        ->data_bind("value: exchange_rate, enable: enableExchangeRate, valueUpdate: 'afterkeydown'"); ?>

                        <?php echo Former::text('')
                        ->label(trans('texts.converted_amount'))
                        ->data_bind("value: convertedAmount, enable: enableExchangeRate")
                        ->append('<span data-bind="html: exchangeCurrencyCode"></span>'); ?>

                    </div>
                    <?php if(!$payment): ?>
                        <?php echo Former::checkbox('email_receipt')
                        ->onchange('onEmailReceiptChange()')
                        ->label('&nbsp;')
                        ->text(trans('texts.email_receipt_to_vendor'))
                        ->value(1); ?>

                    <?php endif; ?>

                </div>
            </div>

        </div>
    </div>

    <?php if(Auth::user()->canCreateOrEdit(ENTITY_PAYMENT, $payment)): ?>
        <center class="buttons">
            <?php echo Button::normal(trans('texts.cancel'))->appendIcon(Icon::create('remove-circle'))->asLinkTo(HTMLUtils::previousUrl('/payments'))->large(); ?>

            <?php if(!$payment || !$payment->is_deleted): ?>
                <?php echo Button::success(trans('texts.save'))->withAttributes(['id' => 'saveButton'])->appendIcon(Icon::create('floppy-disk'))->submit()->large(); ?>

            <?php endif; ?>

            <?php if($payment): ?>
                <?php echo DropdownButton::normal(trans('texts.more_actions'))
                ->withContents($actions)
                ->large()
                ->dropup(); ?>

            <?php endif; ?>

        </center>
    <?php endif; ?>

    <?php echo $__env->make('partials/refund_payment', array_except(get_defined_vars(), array('__data', '__path')))->render(); ?>

    <?php echo Former::close(); ?>


    <script type="text/javascript">

        var bills = <?php echo $bills; ?>;
        var vendors = <?php echo $vendors; ?>;

        var vendorMap = {};
        var billMap = {};
        var billsForVendorMap = {};
        var statuses = [];

        <?php $__currentLoopData = cache('invoiceStatus'); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $status): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
            statuses[<?php echo e($status->id); ?>] = "<?php echo e($status->getTranslatedName()); ?>";
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>

        for (var i = 0; i < vendors.length; i++) {
            var vendor = vendors[i];
            vendorMap[vendor.public_id] = vendor;
        }

        for (var i = 0; i < bills.length; i++) {
            var bill = bills[i];
            var vendor = bill.vendor;

            if (!billsForVendorMap.hasOwnProperty(vendor.public_id)) {
                billsForVendorMap[vendor.public_id] = [];
            }

            billsForVendorMap[vendor.public_id].push(bill);
            billMap[bill.public_id] = bill;
        }

        $(function () {
            <?php if(! empty($totalCredit)): ?>
            $('#payment_type_id option:contains("<?php echo e(trans('texts.apply_credit')); ?>")').text("<?php echo e(trans('texts.apply_credit')); ?> | <?php echo e($totalCredit); ?>");
            <?php endif; ?>

            <?php if(Input::old('data')): ?>
            // this means we failed so we'll reload the previous state
            window.model = new ViewModel(<?php echo $data; ?>);
            <?php else: ?>
            // otherwise create blank model
            window.model = new ViewModel(<?php echo $payment; ?>);
            <?php endif; ?>
            ko.applyBindings(model);

            $('#amount').change(function () {
                var amount = $('#amount').val();
                model.amount(NINJA.parseFloat(amount));
            });

            <?php if($payment): ?>
            $('#payment_date').datepicker('update', '<?php echo e($payment->payment_date); ?>');
            <?php if($payment->payment_type_id != PAYMENT_TYPE_CREDIT): ?>
            $("#payment_type_id option[value='<?php echo e(PAYMENT_TYPE_CREDIT); ?>']").remove();
            <?php endif; ?>
            <?php else: ?>
            $('#payment_date').datepicker('update', new Date());
            populateBillComboboxes(<?php echo e($vendorPublicId); ?>, <?php echo e($billPublicId); ?>);
            <?php endif; ?>

            $('#payment_type_id').combobox();

            <?php if(!$payment && !$vendorPublicId): ?>
            $('.vendor-select input.form-control').focus();
            <?php elseif(!$payment && !$billPublicId): ?>
            $('.bill-select input.form-control').focus();
            <?php elseif(!$payment): ?>
            $('#amount').focus();
            <?php endif; ?>

            $('.payment_date .input-group-addon').click(function () {
                toggleDatePicker('payment_date');
            });

            $('#exchange_currency_id').on('change', function () {
                setTimeout(function () {
                    model.updateExchangeRate();
                }, 1);
            });

            if (isStorageSupported()) {
                if (localStorage.getItem('last:send_email_receipt')) {
                    $('#email_receipt').prop('checked', true);
                }
            }
        });

        function onFormSubmit(event) {
            if ($('#saveButton').is(':disabled')) {
                return false;
            }

            <?php if($payment): ?>
            $('#saveButton').attr('disabled', true);
            return true;
            <?php else: ?>
            // warn if amount is more than balance/credit will be created
            var billId = $('input[name=bill]').val();
            var bill = billMap[billId];
            var amount = $('#amount').val();

            
            
            
            
            
            
            

            <?php endif; ?>
        }

        function submitAjax() {
            $.post('<?php echo e(url($url)); ?>',
                $('.main-form').serialize(),
                function (data) {
                    if (data && data.toLowerCase().indexOf('http') === 0) {
                        NINJA.formIsChanged = false;
                        location.href = data;
                    } else {
                        handleSaveFailed();
                    }
                }).fail(function (data) {
                handleSaveFailed(data);
            });
        }

        function handleSaveFailed(data) {
            $('#saveButton').attr('disabled', false);
            var error = '';
            if (data) {
                var error = firstJSONError(data.responseJSON) || data.statusText;
            }

            swal(<?php echo json_encode(trans('texts.error_refresh_page')); ?>, error);
        }

        function submitAction(action) {
            $('#action').val(action);
            $('.main-form').submit();
        }

        function submitForm_payment(action) {
            submitAction(action);
        }

        function onDeleteClick() {
            sweetConfirm(function () {
                submitAction('delete');
            });
        }

        function onEmailReceiptChange() {
            if (!isStorageSupported()) {
                return;
            }
            var checked = $('#email_receipt').is(':checked');
            localStorage.setItem('last:send_email_receipt', checked ? true : '');
        }


        var ViewModel = function (data) {
            var self = this;

            self.vendor_id = ko.observable();
            self.exchange_currency_id = ko.observable();
            self.amount = ko.observable();
            self.exchange_rate = ko.observable(1);
            self.convert_currency = ko.observable(<?php echo e(($payment && $payment->isExchanged()) ? 'true' : 'false'); ?>);

            if (data) {
                ko.mapping.fromJS(data, self.mapping, this);
                self.exchange_rate(roundSignificant(self.exchange_rate()));
            }

            self.account_currency_id = ko.observable(<?php echo e($account->getCurrencyId()); ?>);

            self.convertedAmount = ko.computed({
                read: function () {
                    return roundToTwo(self.amount() * self.exchange_rate()).toFixed(2);
                },
                write: function (value) {
                    var amount = NINJA.parseFloat(value) / self.amount();
                    self.exchange_rate(roundSignificant(amount));
                }
            }, self);


            self.updateExchangeRate = function () {
                var fromCode = self.paymentCurrencyCode();
                var toCode = self.exchangeCurrencyCode();
                if (currencyMap[fromCode].exchange_rate && currencyMap[toCode].exchange_rate) {
                    var rate = fx.convert(1, {
                        from: fromCode,
                        to: toCode,
                    });
                    self.exchange_rate(roundToFour(rate, true));
                } else {
                    self.exchange_rate(1);
                }
            };

            self.getCurrency = function (currencyId) {
                return currencyMap[currencyId || self.account_currency_id()];
            };

            self.exchangeCurrencyCode = ko.computed(function () {
                var currency = self.getCurrency(self.exchange_currency_id());
                return currency ? currency.code : '';
            });

            self.paymentCurrencyCode = ko.computed(function () {
                var vendor = vendorMap[self.vendor_id()];
                if (vendor && vendor.currency_id) {
                    var currencyId = vendor.currency_id;
                } else {
                    var currencyId = self.account_currency_id();
                }
                var currency = self.getCurrency(currencyId);
                return currency ? currency.code : '';
            });

            self.enableExchangeRate = ko.computed(function () {
                if (self.convert_currency()) {
                    return true;
                }
                /*
                var expenseCurrencyId = self.expense_currency_id() || self.account_currency_id();
                var billCurrencyId = self.bill_currency_id() || self.account_currency_id();
                return expenseCurrencyId != billCurrencyId
                || billCurrencyId != self.account_currency_id()
                || expenseCurrencyId != self.account_currency_id();
                */
            })
        };

        function populateBillComboboxes(vendorId, billId) {
            var $vendorSelect = $('select#vendor');
            $vendorSelect.append(new Option('', ''));
            for (var i = 0; i < vendors.length; i++) {
                var vendor = vendors[i];
                // var vendorName = getVendorDisplayName(vendor);
                // if (!vendorName) {
                //     continue;
                // }
                $vendorSelect.append(new Option(vendor.name, vendor.public_id));
            }

            if (vendorId) {
                $vendorSelect.val(vendorId);
            }

            $vendorSelect.combobox({highlighter: comboboxHighlighter});
            $vendorSelect.on('change', function (e) {
                var vendorId = $('input[name=vendor]').val();
                var billId = $('input[name=bill]').val();
                var bill = billMap[billId];
                if (bill && bill.vendor.public_id == vendorId) {
                    e.preventDefault();
                    return;
                }
                setComboboxValue($('.bill-select'), '', '');
                $billCombobox = $('select#bill');
                $billCombobox.find('option').remove().end().combobox('refresh');
                $billCombobox.append(new Option('', ''));
                var list = vendorId ? (billsForVendorMap.hasOwnProperty(vendorId) ? billsForVendorMap[vendorId] : []) : bills;
                for (var i = 0; i < list.length; i++) {
                    var bill = list[i];
                    var vendor = vendorMap[bill.vendor.public_id];
                    //|| !getVendorDisplayName(vendor)
                    if (!vendor) continue; // vendor is deleted/archived
                    // getVendorDisplayName(vendor)
                    $billCombobox.append(new Option(bill.bill_number + ' - ' + statuses[bill.bill_status.id] + ' - ' +
                        vendor.name + ' - ' + formatMoneyInvoice(bill.amount, bill) + ' | ' +
                        formatMoneyInvoice(bill.balance, bill), bill.public_id));
                }
                $('select#bill').combobox('refresh');
                $('#amount').val('');

                if (window.model) {
                    model.amount('');
                    model.vendor_id(vendorId);
                    setTimeout(function () {
                        model.updateExchangeRate();
                    }, 1);
                }
            });

            if (vendorId) {
                $vendorSelect.trigger('change');
            }

            var $billSelect = $('select#bill').on('change', function (e) {
                $vendorCombobox = $('select#vendor');
                var billId = $('input[name=bill]').val();
                if (billId) {
                    var bill = billMap[billId];
                    var vendor = vendorMap[bill.vendor.public_id];
                    bill.vendor = vendor;
                    setComboboxValue($('.vendor-select'), vendor.public_id, vendor.name); //getVendorDisplayName(vendor)
                    var amount = parseFloat(bill.balance);
                    $('#amount').val(amount.toFixed(2));
                    model.amount(amount);
                } else {
                    $('#amount').val('');
                    model.amount('');
                }
                model.vendor_id(vendor ? vendor.public_id : 0);
                setTimeout(function () {
                    model.updateExchangeRate();
                }, 1);
            });

            $billSelect.combobox({highlighter: comboboxHighlighter});

            if (billId) {
                var bill = billMap[billId];
                if (bill) {
                    var vendor = vendorMap[bill.vendor.public_id];
                    bill.vendor = vendor;
                    //getVendorDisplayName(vendor)
                    setComboboxValue($('.bill-select'), bill.public_id, (bill.bill_number + ' - ' +
                        bill.bill_status.name + ' - ' + vendor.name + ' - ' +
                        formatMoneyInvoice(bill.amount, bill) + ' | ' + formatMoneyInvoice(bill.balance, bill)));
                    $billSelect.trigger('change');
                }
            } else if (vendorId) {
                var vendor = vendorMap[vendorId];
                //getVendorDisplayName(vendor)
                setComboboxValue($('.vendor-select'), vendor.public_id, vendor.name);
                $vendorSelect.trigger('change');
            } else {
                $vendorSelect.trigger('change');
            }
        }

    </script>

<?php $__env->stopSection(); ?>

<?php echo $__env->make('header', array_except(get_defined_vars(), array('__data', '__path')))->render(); ?>