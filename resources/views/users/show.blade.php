@extends('header')

@section('head')
    @parent

@stop

@section('content')
    <!-- edit user -->
    <div class="row">
        <div class="col-md-7">
            <ol class="breadcrumb">
                <li>{{ link_to('/users', trans('texts.users')) }}</li>
                <li class='active'>{{ $user->present()->fullName }}</li> {!! $user->present()->statusLabel !!}
            </ol>
        </div>
        <div class="col-md-5">
            <div class="pull-right">
                {!! Former::open('users/bulk')->autocomplete('off')->addClass('mainForm') !!}
                <div style="display:none">
                    {!! Former::text('action') !!}
                    {!! Former::text('public_id')->value($user->public_id) !!}
                </div>

                @if (!$user->is_deleted)
                    @can('edit', $user)
                        {!! DropdownButton::normal(trans('texts.edit_user'))
                            ->withAttributes(['class'=>'normalDropDown'])
                            ->withContents([
                              ($user->trashed() ? false : ['label' => trans('texts.archive_user'), 'url' => "javascript:onArchiveClick()"]),
                              ['label' => trans('texts.delete_user'), 'url' => "javascript:onDeleteClick()"],
                              auth()->user()->is_admin ? \DropdownButton::DIVIDER : false,
                              auth()->user()->is_admin ? ['label' => trans('texts.purge_user'), 'url' => "javascript:onPurgeClick()"] : false,
                            ]
                          )->split() !!}
                    @endcan
                @endif
                @if ($user->trashed())
                    @can('edit', $user)
                        @if (auth()->user()->is_admin && $user->is_deleted)
                            {!! Button::danger(trans('texts.purge_user'))
                                    ->appendIcon(Icon::create('warning-sign'))
                                    ->withAttributes(['onclick' => 'onPurgeClick()']) !!}
                        @endif
                        {!! Button::primary(trans('texts.restore_user'))
                                ->appendIcon(Icon::create('retweet'))
                                ->withAttributes(['onclick' => 'onRestoreClick()']) !!}
                    @endcan
                @endif
                {!! Former::close() !!}
            </div>
        </div>
    </div>
    <!-- user detail -->
    <div class="panel panel-default">
        <div class="panel-body">
            <div class="row">
                <div class="col-md-3">
                    <h3>{{ trans('texts.user_details') }}</h3>
                    @if ($user)
                        <p><i class="fa fa-id-number"
                              style="width: 20px"></i>{{ trans('texts.id_number').': '.$user->id }}</p>
                    @endif
                    @if ($user->first_name)
                        <p><i class="fa fa-vat-number"
                              style="width: 20px"></i>{{ trans('texts.first_name').': '. $user->present()->fullName }}
                        </p>
                    @endif
                    @if ($user->notes)
                        <p><i>{!! nl2br(e($user->notes)) !!}</i></p>
                    @endif
                    @if ($user->last_login)
                        <h3 style="margin-top:0px"><small>
                                {{ trans('texts.last_logged_in') }} {{ Utils::timestampToDateTimeString(strtotime($user->last_login)) }}
                            </small>
                        </h3>
                    @endif
                </div>
                <div class="col-md-3">
                    <h3>{{ trans('texts.address') }}</h3>
                    <p>address details</p>
                </div>
                <div class="col-md-3">
                    <h3>{{ trans('texts.contacts') }}</h3>
                    @if ($user->email)
                        <i class="fa fa-envelope"
                           style="width: 20px"></i>{!! HTML::mailto($user->email, $user->email) !!}<br/>
                    @endif
                    @if ($user->phone)
                        <i class="fa fa-phone" style="width: 20px"></i>{{ $user->phone }}<br/>
                    @endif
                    <br/>
                </div>
            </div>
        </div>
    </div>
    <ul class="nav nav-tabs nav-justified">
        {!! Form::tab_link('#activity', trans('texts.activity'), true) !!}
        @if (true)
            {!! Form::tab_link('#permissions', trans('texts.permissions')) !!}
        @endif
        @if (true)
            {!! Form::tab_link('#groups', trans('texts.groups')) !!}
        @endif
    </ul>
    <br/>
    <div class="tab-content">
        @if (true)
            <div class="tab-pane" id="permissions">
                @include('accounts.permission',[
                'user' => $user,
                'permissions' => $permissions,
                'userPermissions' => $userPermissions,
                ])
            </div>
        @endif

        @if (true)
            <div class="tab-pane" id="groups">
                <h4>groups</h4>
            </div>
        @endif

    </div>
    <div class="modal fade" id="emailHistoryModal" tabindex="-1" role="dialog"
         aria-labelledby="emailHistoryModalLabel"
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
    <script nonce="{{ csrf_token() }}">
        $(document).ready(function () {
            $('input').iCheck({
                checkboxClass: 'icheckbox_square',
                radioClass: 'iradio_square',
                // radioClass: 'iradio_flat-orange',
                increaseArea: '5%',
            });
            //
            // function submitChangePermission() {
            // $('tr.header-row input:radio').on('ifChanged', function () {
            //     value = $(this).attr('value');
            //     console.log(value);
            //     area = $(this).data('checker-group');
            //     $('.radiochecker-' + area + '[value=' + value + ']').iCheck('check');
            // });
            // var arr = $('ínput[type="radio"]', $('#mainForm'));
            // for (var i = 0; i < arr.length; i++) {
            //     alert(arr[i]);
            // }
            // console.log($('mainForm #permission').val());
            {{--$.ajax({--}}
            {{--    type: 'POST',--}}
            {{--    url: '{{ URL::to('/users/change_password') }}',--}}
            {{--    data: 'current_password=' + encodeURIComponent($('form #current_password').val()) +--}}
            {{--        '&new_password=' + encodeURIComponent($('form #newer_password').val()) +--}}
            {{--        '&confirm_password=' + encodeURIComponent($('form #confirm_password').val()),--}}
            {{--    success: function (result) {--}}
            {{--        if (result == 'success') {--}}
            {{--            NINJA.formIsChanged = false;--}}
            {{--            $('#changePasswordButton').hide();--}}
            {{--            $('#successDiv').show();--}}
            {{--            $('#cancelChangePasswordButton').html('{{ trans('texts.close') }}');--}}
            {{--        } else {--}}
            {{--            $('#changePasswordError').html(result);--}}
            {{--            $('#changePasswordDiv').show();--}}
            {{--        }--}}
            {{--        $('#changePasswordFooter').show();--}}
            {{--        $('#working').hide();--}}
            {{--    }--}}
            {{--});--}}
            // }

            // Check/Uncheck all radio buttons in the group
            $('tr.header-row input:radio').on('ifChanged', function () {
                value = $(this).attr('value');
                area = $(this).data('checker-group');
                $('.radiochecker-' + area + '[value=' + value + ']').iCheck('check');
            });

            $('.header-name').click(function () {
                $(this).parent().nextUntil('tr.header-row').slideToggle(1);
            });

            $('.tooltip-base').tooltip({container: 'body'});
            $(".superuser").change(function () {
                var perms = $(this).val();
                if (perms == '1') {
                    $("#nonadmin").hide();
                } else {
                    $("#nonadmin").show();
                }
            });
        });
    </script>
    <script type="text/javascript">
        var loadedTabs = {};
        $(function () {
            $('.normalDropDown:not(.dropdown-toggle)').click(function (event) {
                openUrlOnClick('{{ URL::to('users/' . $user->public_id . '/edit') }}', event);
            });

            // load datatable data when tab is shown and remember last tab selected
            $('a[data-toggle="tab"]').on('shown.bs.tab', function (e) {
                var target = $(e.target).attr("href") // activated tab
                target = target.substring(1);
                if (isStorageSupported()) {
                    localStorage.setItem('permission_tab', target);
                }
                if (!loadedTabs.hasOwnProperty(target) && window['load_' + target]) {
                    loadedTabs[target] = true;
                    window['load_' + target]();
                }
            });

            var tab = window.location.hash || (localStorage.getItem('permission_tab') || '');
            tab = tab.replace('#', '');
            var selector = '.nav-tabs a[href="#' + tab + '"]';

            if (tab && tab != 'permission' && $(selector).length && window['load_' + tab]) {
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

        function onUpdateClick() {
            $('#action').val('update');
            $('.mainForm').submit();
        }

        @if ($user->showMap())
        function initialize() {
            var mapCanvas = document.getElementById('map');
            var mapOptions = {
                zoom: {{ DEFAULT_MAP_ZOOM }},
                mapTypeId: google.maps.MapTypeId.ROADMAP,
                zoomControl: true,
            };

            var map = new google.maps.Map(mapCanvas, mapOptions)
            var address = {!! json_encode(e("{$user->address1} {$user->address2} {$user->city} {$user->state} {$user->postal_code} " . ($user->country ? $user->country->getName() : ''))) !!};

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
