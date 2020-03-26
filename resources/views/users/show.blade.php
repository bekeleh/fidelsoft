@extends('header')

@section('head')
    @parent
    <script src="{{ asset('js/select2.min.js') }}" type="text/javascript"></script>
    <link href="{{ asset('css/select2.css') }}" rel="stylesheet" type="text/css"/>
    @if ($user->showMap())
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
                <li>{{ link_to('/users', trans('texts.users')) }}</li>
            </ol>
        </div>
        <div class="col-md-5">
            <div class="pull-right">
                {!! Former::open('users/bulk')->autocomplete('off')->addClass('mainForm') !!}
                <div style="display:none">
                    {!! Former::text('action') !!}
                    {!! Former::text('public_id')->value($user->public_id) !!}
                </div>
            </div>
        </div>
    </div>
    <div class="panel panel-default">
        <div class="panel-body">
            <div class="row">
                <p> user detail here</p>
            </div>
        </div>
    </div>
    @if ($user->showMap())
        <div id="map">
        </div>
        <br/>
    @endif

    <ul class="nav nav-tabs nav-justified">
        {!! Form::tab_link('#permission', trans('texts.permission'), true) !!}
        {!! Form::tab_link('#group', trans('texts.groups')) !!}
    </ul><br/>
    <div class="tab-pane" id="permission">
        <p>list of permissions</p>
    </div>
    <div class="tab-pane" id="group">
        <p> list of groups</p>
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
                window['load_permission']();
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
            }, "{{ trans('texts.purge_user_warning') . "\\n\\n" . trans('texts.mobile_refresh_warning') . "\\n\\n" . trans('texts.no_undo') }}");
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
