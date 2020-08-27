@extends('header')

@section('head')
    @parent

    <script src="{{ asset('js/select2.min.js') }}" type="text/javascript"></script>
    <link href="{{ asset('css/select2.css') }}" rel="stylesheet" type="text/css"/>

    @if ($vendor->showMap())
        <style>
            #map {
                width: 100%;
                height: 200px;
                border-width: 1px;
                border-style: solid;
                border-color: #ddd;
            }
        </style>
        <script src="https://maps.googleapis.com/maps/api/js?key={{ env('GOOGLE_MAPS_API_KEY') }}"></script>
    @endif
@stop

@section('content')
    <div class="row">
        <div class="col-md-7">
            <ol class="breadcrumb">
                <li>{{ link_to('/vendors', trans('texts.vendors')) }}</li>
                <li class='active'>{{ $vendor->getDisplayName() }}</li> {!! $vendor->present()->statusLabel !!}
            </ol>
        </div>
        <div class="col-md-5">
            <div class="pull-right">
                {!! Former::open('vendors/bulk')->autocomplete('off')->addClass('mainForm') !!}
                <div style="display:none">
                    {!! Former::text('action') !!}
                    {!! Former::text('public_id')->value($vendor->public_id) !!}
                </div>

                @if ($gatewayLink)
                    {!! Button::normal(trans('texts.view_in_gateway', ['gateway'=>$gatewayName]))
                    ->asLinkTo($gatewayLink)
                    ->withAttributes(['target' => '_blank']) !!}
                @endif

                @if (!$vendor->is_deleted)
                    @can('edit', $vendor)
                        {!! DropdownButton::normal(trans('texts.edit_vendor'))
                        ->withAttributes(['class'=>'normalDropDown'])
                        ->withContents([
                        ($vendor->trashed() ? false : ['label' => trans('texts.archive_vendor'), 'url' => "javascript:onArchiveClick()"]),
                        ['label' => trans('texts.delete_vendor'), 'url' => "javascript:onDeleteClick()"],
                        auth()->user()->is_admin ? DropdownButton::DIVIDER : false,
                        ]
                        )->split() !!}
                    @endcan
                    @if (!$vendor->trashed())
                        @can('create', ENTITY_BILL)
                            {!! DropdownButton::primary(trans('texts.view_statement'))
                            ->withAttributes(['class'=>'primaryDropDown'])
                            ->withContents($actionLinks)->split() !!}
                        @endcan
                    @endif
                @endif
                @if ($vendor->trashed())
                    @can('edit', $vendor)
                        {!! Button::primary(trans('texts.restore_vendor'))
                        ->appendIcon(Icon::create('retweet'))
                        ->withAttributes(['onclick' => 'onRestoreClick()']) !!}
                    @endcan
                @endif
                {!! Former::close() !!}
            </div>
        </div>
    </div>
    @if ($vendor->last_login)
        <h3 style="margin-top:0px"><small>
                {{ trans('texts.last_logged_in') }} {{ Utils::timestampToDateTimeString(strtotime($vendor->last_login)) }}
            </small>
        </h3>
    @endif
    <div class="panel panel-default">
        <div class="panel-body">
            <div class="row">
                <div class="col-md-3">
                    <h3>{{ trans('texts.details') }}</h3>
                    @if ($vendor->id_number)
                        <p><i class="fa fa-id-number"
                              style="width: 20px"></i>{{ trans('texts.id_number').': '.$vendor->id_number }}</p>
                    @endif
                    @if ($vendor->vat_number)
                        <p><i class="fa fa-vat-number"
                              style="width: 20px"></i>{{ trans('texts.vat_number').': '.$vendor->vat_number }}</p>
                    @endif

                    @if ($vendor->account->customLabel('vendor1') && $vendor->custom_value1)
                        {{ $vendor->account->present()->customLabel('vendor1') . ': ' }} {!! nl2br(e($vendor->custom_value1)) !!}
                        <br/>
                    @endif
                    @if ($vendor->account->customLabel('vendor2') && $vendor->custom_value2)
                        {{ $vendor->account->present()->customLabel('vendor2') . ': ' }} {!! nl2br(e($vendor->custom_value2)) !!}
                        <br/>
                    @endif

                    @if ($vendor->work_phone)
                        <i class="fa fa-phone" style="width: 20px"></i>{{ $vendor->work_phone }}
                    @endif

                    @if (floatval($vendor->task_rate))
                        <p>{{ trans('texts.task_rate') }}: {{ Utils::roundSignificant($vendor->task_rate) }}</p>
                    @endif
                    @if ($vendor->public_notes)
                        <p><i>{!! nl2br(e($vendor->public_notes)) !!}</i></p>
                    @endif
                    @if ($vendor->private_notes)
                        <p><i>{!! nl2br(e($vendor->private_notes)) !!}</i></p>
                    @endif
                    @if ($vendor->industry || $vendor->size)
                        @if ($vendor->industry)
                            {{ $vendor->industry->name }}
                        @endif
                        @if ($vendor->industry && $vendor->size)
                            |
                        @endif
                        @if ($vendor->size)
                            {{ $vendor->size->name }}<br/>
                        @endif
                    @endif
                    @if ($vendor->website)
                        <p>{!! Utils::formatWebsite($vendor->website) !!}</p>
                    @endif
                    @if ($vendor->language)
                        <p><i class="fa fa-language" style="width: 20px"></i>{{ $vendor->language->name }}</p>
                    @endif
                    <p>{{ trans('texts.payment_terms').': '.trans('texts.payment_terms_net')}} {{ $vendor->present()->paymentTerms }}</p>
                    <!--- vendor vendor type -->
                    <p>{{ trans('texts.vendor_type_name').': '}} {{ $vendor->present()->vendorType}}</p>
                    <!--- vendor hold reason -->
                    <p>{{ trans('texts.hold_reason_name').': '}}{{ $vendor->present()->holdReason}}</p>
                    <div class="text-muted" style="padding-top:8px">
                        @if ($vendor->show_tasks_in_portal)
                            • {{ trans('texts.can_view_tasks') }}<br/>
                        @endif
                        @if ($vendor->account->hasReminders() && ! $vendor->send_reminders)
                            • {{ trans('texts.is_not_sent_reminders') }}</br>
                        @endif
                    </div>
                </div>
                <div class="col-md-3">
                    <h3>{{ trans('texts.address') }}</h3>
                    @if ($vendor->addressesMatch())
                        {!! $vendor->present()->address(ADDRESS_BILLING) !!}
                    @else
                        {!! $vendor->present()->address(ADDRESS_BILLING, true) !!}<br/>
                        {!! $vendor->present()->address(ADDRESS_SHIPPING, true) !!}
                    @endif
                </div>

                <div class="col-md-3">
                    <h3>{{ trans('texts.vendor_contacts') }}</h3>
                    @foreach ($vendor->contacts as $contact)
                        @if ($contact->first_name || $contact->last_name)
                            <b>{{ $contact->first_name.' '.$contact->last_name }}</b><br/>
                        @endif
                        @if ($contact->email)
                            <i class="fa fa-envelope"
                               style="width: 20px"></i>{!! HTML::mailto($contact->email, $contact->email) !!}<br/>
                        @endif
                        @if ($contact->phone)
                            <i class="fa fa-phone" style="width: 20px"></i>{{ $contact->phone }}<br/>
                        @endif

                        @if ($vendor->account->customLabel('contact1') && $contact->custom_value1)
                            {{ $vendor->account->present()->customLabel('contact1') . ': ' . $contact->custom_value1 }}
                            <br/>
                        @endif
                        @if ($vendor->account->customLabel('contact2') && $contact->custom_value2)
                            {{ $vendor->account->present()->customLabel('contact2') . ': ' . $contact->custom_value2 }}
                            <br/>
                        @endif

                        @if (Auth::user()->confirmed && $vendor->account->enable_vendor_portal)
                            <i class="fa fa-dashboard" style="width: 20px"></i>
                            <a href="{{ $contact->link }}"
                               onclick="window.open('{{ $contact->link }}?silent=true', '_blank');return false;">
                                {{ trans('texts.view_in_portal') }}
                            </a>
                            @if (config('services.postmark'))
                                | <a href="#" onclick="showEmailHistory('{{ $contact->email }}')">
                                    {{ trans('texts.email_history') }}
                                </a>
                            @endif
                            <br/>
                        @endif
                        <br/>
                    @endforeach
                </div>

                <div class="col-md-3">
                    <h3>{{ trans('texts.standing') }}
                        <table class="table" style="width:100%">
                            <tr>
                                <td><small>{{ trans('texts.paid_to_date') }}</small></td>
                                <td style="text-align: left">{{ Utils::formatMoney($vendor->paid_to_date, $vendor->getCurrencyId()) }}
                                </td>
                            </tr>
                            <tr>
                                <td><small>{{ trans('texts.balance') }}</small></td>
                                <td style="text-align: left">{{ Utils::formatMoney($vendor->balance, $vendor->getCurrencyId()) }}
                                </td>
                            </tr>
                            @if ($credit > 0)
                                <tr>
                                    <td><small>{{ trans('texts.bill_credit') }}</small></td>
                                    <td style="text-align: left">{{ Utils::formatMoney($credit, $vendor->getCurrencyId()) }}
                                    </td>
                                </tr>
                            @endif
                        </table>
                    </h3>
                </div>
            </div>
        </div>
    </div>

    @if ($vendor->showMap())
        <div id="map"></div>
        <br/>
    @endif

    <ul class="nav nav-tabs nav-justified">
        {!! Form::tab_link('#activity', trans('texts.activity'), true) !!}
        @if ($hasExpenses)
            {!! Form::tab_link('#expenses', trans('texts.expenses')) !!}
        @endif
        @if ($hasQuotes)
            {!! Form::tab_link('#quotes', trans('texts.quotes')) !!}
        @endif
        @if ($hasRecurringInvoices)
            {!! Form::tab_link('#recurring_bills', trans('texts.recurring')) !!}
        @endif
        {!! Form::tab_link('#bills', trans('texts.bills')) !!}
        {!! Form::tab_link('#payments', trans('texts.pay_payments')) !!}
        @if ($account->isModuleEnabled(ENTITY_VENDOR_CREDIT))
            {!! Form::tab_link('#credits', trans('texts.bill_credit')) !!}
        @endif
    </ul>
    <br/>

    <div class="tab-content">
        <div class="tab-pane active" id="activity">
            {!! Datatable::table()
                ->addColumn(
                trans('texts.date'),
                trans('texts.message'),
                trans('texts.balance'),
                trans('texts.adjustment'))
                ->setUrl(url('api/vendor/activities/'. $vendor->public_id))
                ->setCustomValues('entityType', 'activity')
                ->setCustomValues('vendorId', $vendor->public_id)
                ->setCustomValues('rightAlign', [2, 3])
                ->setOptions('sPaginationType', 'bootstrap')
                ->setOptions('bFilter', true)
                ->setOptions('aaSorting', [['0', 'desc']])
                ->render('datatable') !!}
        </div>

        @if ($hasExpenses)
            <div class="tab-pane" id="expenses">
                @include('list', [
                'entityType' => ENTITY_BILL_EXPENSE,
                'datatable' => new \App\Ninja\Datatables\BillExpenseDatatable(true, true),
                'vendorId' => $vendor->public_id,
                'url' => url('api/vendor_expenses/' . $vendor->public_id),
                ])
            </div>
        @endif

        @if (Utils::hasFeature(FEATURE_QUOTES) && $hasQuotes)
            <div class="tab-pane" id="quotes">
                @include('list', [
                'entityType' => ENTITY_BILL_QUOTE,
                'datatable' => new \App\Ninja\Datatables\BillDatatable(true, true, ENTITY_BILL_QUOTE),
                'vendorId' => $vendor->public_id,
                'url' => url('api/bill_quotes/' . $vendor->public_id),
                ])
            </div>
        @endif

        @if ($hasRecurringInvoices)
            <div class="tab-pane" id="recurring_bills">
                @include('list', [
                'entityType' => ENTITY_RECURRING_BILL,
                'datatable' => new \App\Ninja\Datatables\RecurringBillDatatable(true, true),
                'vendorId' => $vendor->public_id,
                'url' => url('api/recurring_bills/' . $vendor->public_id),
                ])
            </div>
        @endif

        <div class="tab-pane" id="bills">
            @include('list', [
            'entityType' => ENTITY_BILL,
            'datatable' => new \App\Ninja\Datatables\BillDatatable(true, true),
            'vendorId' => $vendor->public_id,
            'url' => url('api/bills/' . $vendor->public_id),
            ])
        </div>

        <div class="tab-pane" id="payments">
            @include('list', [
            'entityType' => ENTITY_BILL_PAYMENT,
            'datatable' => new \App\Ninja\Datatables\BillPaymentDatatable(true, true),
            'vendorId' => $vendor->public_id,
            'url' => url('api/bill_payments/' . $vendor->public_id),
            ])
        </div>

        @if ($account->isModuleEnabled(ENTITY_VENDOR_CREDIT))
            <div class="tab-pane" id="credits">
                @include('list', [
                'entityType' => ENTITY_VENDOR_CREDIT,
                'datatable' => new \App\Ninja\Datatables\VendorCreditDatatable(true, true),
                'vendorId' => $vendor->public_id,
                'url' => url('api/vendor_credits/' . $vendor->public_id),
                ])
            </div>
        @endif
    </div>
    <div class="modal fade" id="emailHistoryModal" tabindex="-1" role="dialog" aria-labelledby="emailHistoryModalLabel"
         aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
                    <h4 class="modal-title" id="myModalLabel">{{ trans('texts.email_history') }}</h4>
                </div>

                <div class="container" style="width: 100%; padding-bottom: 0px !important">
                    <div class="panel panel-default">
                        <div class="panel-body">

                        </div>
                    </div>
                </div>

                <div class="modal-footer" id="signUpFooter" style="margin-top: 0px">
                    <button type="button" class="btn btn-default"
                            data-dismiss="modal">{{ trans('texts.close') }} </button>
                    <button type="button" class="btn btn-danger" onclick="onReactivateClick()" id="reactivateButton"
                            style="display:none;">{{ trans('texts.reactivate') }} </button>
                </div>
            </div>
        </div>
    </div>
    <script type="text/javascript">
        var loadedTabs = {};
        $(function () {
            $('.normalDropDown:not(.dropdown-toggle)').click(function (event) {
                openUrlOnClick('{{ URL::to('vendors/' . $vendor->public_id . '/edit') }}', event);
            });
            $('.primaryDropDown:not(.dropdown-toggle)').click(function (event) {
                openUrlOnClick('{{ URL::to('vendors/statement/' . $vendor->public_id ) }}', event);
            });

            // load datatable data when tab is shown and remember last tab selected
            $('a[data-toggle="tab"]').on('shown.bs.tab', function (e) {
                var target = $(e.target).attr("href"); // activated tab
                target = target.substring(1);
                if (isStorageSupported()) {
                    localStorage.setItem('vendor_tab', target);
                }
                if (!loadedTabs.hasOwnProperty(target) && window['load_' + target]) {
                    loadedTabs[target] = true;
                    window['load_' + target]();
                }
            });

            var tab = window.location.hash || (localStorage.getItem('vendor_tab') || '');
            tab = tab.replace('#', '');
            var selector = '.nav-tabs a[href="#' + tab + '"]';

            if (tab && tab != 'activity' && $(selector).length && window['load_' + tab]) {
                $(selector).tab('show');
            } else {
                // window['load_activity']();
            }
        });

        function onArchiveClick() {
            $('#action').val('archive');
            $('.mainForm').submit();
        }

        function onRestoreClick() {
            $('#action').val('restore');
            $('.mainForm').submit();
        }

        function onDeleteClick() {
            sweetConfirm(function () {
                $('#action').val('delete');
                $('.mainForm').submit();
            });
        }

        function onPurgeClick() {
            sweetConfirm(function () {
                $('#action').val('purge');
                $('.mainForm').submit();
            }, "{{ trans('texts.purge_vendor_warning') . "\\n\\n" . trans('texts.mobile_refresh_warning') . "\\n\\n" . trans('texts.no_undo') }}");
        }

        function showEmailHistory(email) {
            window.emailBounceId = false;
            $('#emailHistoryModal .panel-body').html("{{ trans('texts.loading') }}...");
            $('#reactivateButton').hide();
            $('#emailHistoryModal').modal('show');
            $.post('{{ url('/email_history') }}', {email: email}, function (data) {
                $('#emailHistoryModal .panel-body').html(data.str);
                window.emailBounceId = data.bounce_id;
                $('#reactivateButton').toggle(!!window.emailBounceId);
            })
        }

        function onReactivateClick() {
            $.post('{{ url('/reactivate_email') }}/' + window.emailBounceId, function (data) {
                $('#emailHistoryModal').modal('hide');
                swal("{{ trans('texts.reactivated_email') }}")
            })
        }

        @if ($vendor->showMap())
        function initialize() {
            var mapCanvas = document.getElementById('map');
            var mapOptions = {
                zoom: {{ DEFAULT_MAP_ZOOM }},
                mapTypeId: google.maps.MapTypeId.ROADMAP,
                zoomControl: true,
            };

            var map = new google.maps.Map(mapCanvas, mapOptions);
            var address = {!! json_encode(e("{$vendor->address1} {$vendor->address2} {$vendor->city} {$vendor->state} {$vendor->postal_code} " . ($vendor->country ? $vendor->country->getName() : ''))) !!};

            geocoder = new google.maps.Geocoder();
            geocoder.geocode({'address': address}, function (results, status) {
                if (status == google.maps.GeocoderStatus.OK) {
                    if (status != google.maps.GeocoderStatus.ZERO_RESULTS) {
                        var result = results[0];
                        map.setCenter(result.geometry.location);

                        var infowindow = new google.maps.InfoWindow(
                            {
                                content: '<b>' + result.formatted_address + '</b>',
                                size: new google.maps.Size(150, 50)
                            });

                        var marker = new google.maps.Marker({
                            position: result.geometry.location,
                            map: map,
                            title: address,
                        });
                        google.maps.event.addListener(marker, 'click', function () {
                            infowindow.open(map, marker);
                        });
                    } else {
                        $('#map').hide();
                    }
                } else {
                    $('#map').hide();
                }
            });
        }

        google.maps.event.addDomListener(window, 'load', initialize);
        @endif
    </script>
@stop
