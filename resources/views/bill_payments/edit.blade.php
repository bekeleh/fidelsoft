@extends('header')

@section('head')
    @parent

    @include('money_script')

    <style type="text/css">
        .input-group-addon {
            min-width: 40px;
        }
    </style>
@stop

@section('content')
    {!! Former::open($url)
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
        )) !!}
    @if ($payment)
        {!! Former::populate($payment) !!}
    @else
        @if ($account->payment_type_id)
            {!! Former::populateField('payment_type_id', $account->payment_type_id) !!}
        @endif
    @endif

    <span style="display:none">
        {!! Former::text('public_id') !!}
        {!! Former::text('action') !!}
    </span>
    <div class="row">
        <div class="col-lg-10 col-lg-offset-1">
            <div class="panel panel-default">
                <div class="panel-body">
                @if ($payment)
                    {!! Former::plaintext()->label('vendor')->value($payment->vendor->present()->link) !!}
                    {!! Former::plaintext()->label('bill')->value($payment->bill->present()->link) !!}
                    {!! Former::plaintext()->label('amount')->value($payment->present()->amount) !!}
                @else
                    {!! Former::select('vendor')->addOption('', '')->addGroupClass('vendor-select') !!}
                    {!! Former::select('bill')->addOption('', '')->addGroupClass('bill-select') !!}
                    {!! Former::text('amount')->append('<span data-bind="html: paymentCurrencyCode"></span>') !!}

                    @if (isset($paymentTypeId) && $paymentTypeId)
                        {!! Former::populateField('payment_type_id', $paymentTypeId) !!}
                    @endif
                @endif
                <!-- payment type -->
                @if (!$payment || !$payment->account_gateway_id)
                    {!! Former::select('payment_type_id')
                    ->placeholder(trans('select_payment_type'))
                    ->addOption('', '')
                    ->fromQuery($paymentTypes, 'name', 'id')
                    ->addGroupClass('payment-type-select') !!}
                @endif

                <!-- payment status -->
                {!! Former::select('payment_status_id')->addOption('','')
                ->placeholder(trans('select_payment_status'))
                ->fromQuery($paymentStatuses, 'name', 'id')
                ->label(trans('texts.payment_status'))
                !!}
                <!-- payment date -->
                {!! Former::text('payment_date')
                ->data_date_format(Session::get(SESSION_DATE_PICKER_FORMAT))
                ->addGroupClass('payment_date')
                ->append('<i class="glyphicon glyphicon-calendar"></i>') !!}
                <!-- payment reference -->
                {!! Former::text('transaction_reference')->value(time().str_random(4))!!}
                <!-- payment public notes -->
                {!! Former::textarea('public_notes') !!}
                <!-- payment private notes -->
                {!! Former::textarea('private_notes') !!}
                <!-- payment currency -->
                    @if (!$payment || ($payment && ! $payment->isExchanged()))
                        {!! Former::checkbox('convert_currency')
                        ->text(trans('texts.convert_currency'))
                        ->data_bind('checked: convert_currency')
                        ->label(' ')
                        ->value(1) !!}
                    @endif

                    <div style="display:none" data-bind="visible: enableExchangeRate">
                        <br/>
                        <!-- currency -->
                    {!! Former::select('exchange_currency_id')->addOption('','')
                    ->label(trans('texts.currency'))
                    ->data_placeholder(Utils::getFromCache($account->getCurrencyId(), 'currencies')->name)
                    ->data_bind('combobox: exchange_currency_id, disable: true')
                    ->fromQuery($currencies, 'name', 'id') !!}
                    <!-- exchange rate -->
                        {!! Former::text('exchange_rate')
                        ->data_bind("value: exchange_rate, enable: enableExchangeRate, valueUpdate: 'afterkeydown'") !!}
                        {!! Former::text('')
                        ->label(trans('texts.converted_amount'))
                        ->data_bind("value: convertedAmount, enable: enableExchangeRate")
                        ->append('<span data-bind="html: exchangeCurrencyCode"></span>') !!}
                    </div>
                    @if (!$payment)
                        {!! Former::checkbox('email_receipt')
                        ->onchange('onEmailReceiptChange()')
                        ->label('&nbsp;')
                        ->text(trans('texts.email_receipt_to_vendor'))
                        ->value(1) !!}
                    @endif

                </div>
            </div>

        </div>
    </div>

    @if (Auth::user()->canCreateOrEdit(ENTITY_PAYMENT, $payment))
        <center class="buttons">
            {!! Button::normal(trans('texts.cancel'))->appendIcon(Icon::create('remove-circle'))->asLinkTo(HTMLUtils::previousUrl('/payments'))->large() !!}
            @if (!$payment || !$payment->is_deleted)
                {!! Button::success(trans('texts.save'))->withAttributes(['id' => 'saveButton'])->appendIcon(Icon::create('floppy-disk'))->submit()->large() !!}
            @endif

            @if ($payment)
                {!! DropdownButton::normal(trans('texts.more_actions'))
                ->withContents($actions)
                ->large()
                ->dropup() !!}
            @endif

        </center>
    @endif

    @include('partials/refund_payment')

    {!! Former::close() !!}

    <script type="text/javascript">

        var bills = {!! $bills !!};
        var vendors = {!! $vendors !!};

        var vendorMap = {};
        var billMap = {};
        var billsForVendorMap = {};
        var statuses = [];

        @foreach (cache('invoiceStatus') as $status)
            statuses[{{ $status->id }}] = "{{ $status->getTranslatedName() }}";
                @endforeach

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
            @if (! empty($totalCredit))
            $('#payment_type_id option:contains("{{ trans('texts.apply_credit') }}")').text("{{ trans('texts.apply_credit') }} | {{ $totalCredit}}");
            @endif

            @if (Input::old('data'))
            // this means we failed so we'll reload the previous state
            window.model = new ViewModel({!! $data !!});
            @else
            // otherwise create blank model
            window.model = new ViewModel({!! $payment !!});
            @endif
            ko.applyBindings(model);

            $('#amount').change(function () {
                var amount = $('#amount').val();
                model.amount(NINJA.parseFloat(amount));
            });

            @if ($payment)
            $('#payment_date').datepicker('update', '{{ $payment->payment_date }}');
            @if ($payment->payment_type_id != PAYMENT_TYPE_CREDIT)
            $("#payment_type_id option[value='{{ PAYMENT_TYPE_CREDIT }}']").remove();
            @endif
            @else
            $('#payment_date').datepicker('update', new Date());
            populateBillComboboxes({{ $vendorPublicId }}, {{ $billPublicId }});
            @endif

            $('#payment_type_id').combobox();

            @if (!$payment && !$vendorPublicId)
            $('.vendor-select input.form-control').focus();
            @elseif (!$payment && !$billPublicId)
            $('.bill-select input.form-control').focus();
            @elseif (!$payment)
            $('#amount').focus();
            @endif

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

            @if ($payment)
            $('#saveButton').attr('disabled', true);
            return true;
            @else
            // warn if amount is more than balance/credit will be created
            var billId = $('input[name=bill]').val();
            var bill = billMap[billId];
            var amount = $('#amount').val();

            {{--if (NINJA.parseFloat(amount) < bill.balance || confirm("{{ trans('texts.amount_greater_than_balance') }}")) {--}}
            {{--    $('#saveButton').attr('disabled', true);--}}
            {{--    submitAjax();--}}
            {{--    return false;--}}
            {{--} else {--}}
            {{--    return false;--}}
            {{--}--}}

            @endif
        }

        function submitAjax() {
            $.post('{{ url($url) }}',
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

            swal({!! json_encode(trans('texts.error_refresh_page')) !!}, error);
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
            self.convert_currency = ko.observable({{ ($payment && $payment->isExchanged()) ? 'true' : 'false' }});

            if (data) {
                ko.mapping.fromJS(data, self.mapping, this);
                self.exchange_rate(roundSignificant(self.exchange_rate()));
            }

            self.account_currency_id = ko.observable({{ $account->getCurrencyId() }});

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

@stop
