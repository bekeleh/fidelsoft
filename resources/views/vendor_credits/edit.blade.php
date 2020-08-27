@extends('header')

@section('content')
    {!! Former::open($url)
        ->addClass('col-lg-10 col-lg-offset-1 warn-on-exit')
        ->method($method)
        ->rules([
        'vendor_id' => 'required',
        'amount' => 'required',
        'public_notes' => 'required',
        'private_notes' => 'required',
        'credit_date' => 'required',
        ]) !!}
    @if ($credit)
        {!! Former::populate($credit) !!}
        <div style="display:none">
            {!! Former::text('public_id') !!}
        </div>
    @endif
    <div class="row">
        <div class="col-lg-10 col-lg-offset-1">

            <div class="panel panel-default">
                <div class="panel-body">

                    @if ($credit)
                        {!! Former::plaintext()->label('vendor')->value($vendor->present()->link) !!}
                    @else
                        {!! Former::select('vendor_id')
                        ->label('vendor')
                        ->addOption('', '')
                        ->addGroupClass('vendor-select') !!}
                    @endif
                    {!! Former::text('amount') !!}
                    @if ($credit)
                        {!! Former::text('balance') !!}
                    @endif
                    {!! Former::text('credit_date')
                    ->data_date_format(Session::get(SESSION_DATE_PICKER_FORMAT, DEFAULT_DATE_PICKER_FORMAT))
                    ->addGroupClass('credit_date')
                    ->append('<i class="glyphicon glyphicon-calendar"></i>') !!}
                    {!! Former::textarea('public_notes')->rows(4) !!}
                    {!! Former::textarea('private_notes')->rows(4) !!}
                </div>
            </div>

        </div>
    </div>

    @if(Auth::user()->canCreateOrEdit(ENTITY_VENDOR_CREDIT, $credit))
        <center class="buttons">
            {!! Button::normal(trans('texts.cancel'))->large()->asLinkTo(HTMLUtils::previousUrl('/vendor_credits'))->appendIcon(Icon::create('remove-circle')) !!}
            {!! Button::success(trans('texts.save'))->submit()->large()->appendIcon(Icon::create('floppy-disk')) !!}
        </center>
    @endif

    {!! Former::close() !!}

    <script type="text/javascript">
        var vendors = {!! $vendors ?: 'false' !!};

        $(function () {
                    @if ( ! $credit)
            var $vendorSelect = $('select#vendor_id');
            for (var i = 0; i < vendors.length; i++) {
                var vendor = vendors[i];
                // var vendorName = getVendorDisplayName(vendor);
                // if (!vendorName) {
                //     continue;
                // }
                $vendorSelect.append(new Option(vendor.name, vendor.public_id));
            }

            if ({{ $vendorPublicId ? 'true' : 'false' }}) {
                $vendorSelect.val({{ $vendorPublicId }});
            }

            $vendorSelect.combobox({highlighter: comboboxHighlighter});
            @endif

            $('#currency_id').combobox();
            $('#credit_date').datepicker('update', '{{ $credit ? $credit->credit_date : 'new Date()' }}');

            @if (!$vendorPublicId)
            $('.vendor-select input.form-control').focus();
            @else
            $('#amount').focus();
            @endif

            $('.credit_date .input-group-addon').click(function () {
                toggleDatePicker('credit_date');
            });
        });

    </script>

@stop
