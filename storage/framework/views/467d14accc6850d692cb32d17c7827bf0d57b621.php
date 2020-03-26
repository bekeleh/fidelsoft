<?php $__env->startSection('head'); ?>
    ##parent-placeholder-1a954628a960aaef81d7b2d4521929579f3541e6##
    <script src="<?php echo e(asset('js/select2.min.js')); ?>" type="text/javascript"></script>
    <link href="<?php echo e(asset('css/select2.css')); ?>" rel="stylesheet" type="text/css"/>
    <?php if($user->showMap()): ?>
        <style>
            #map {
                width: 100%;
                height: 200px;
                border-width: 1px;
                border-style: solid;
                border-color: #ddd;
            }
        </style>
        <script src="https://maps.googleapis.com/maps/api/js?key=<?php echo e(env('GOOGLE_MAPS_API_KEY')); ?>"></script>
    <?php endif; ?>
<?php $__env->stopSection(); ?>

<?php $__env->startSection('content'); ?>
    <div class="row">
        <div class="col-md-7">
            <ol class="breadcrumb">
                <li><?php echo e(link_to('/users', trans('texts.users'))); ?></li>
            </ol>
        </div>
        <div class="col-md-5">
            <div class="pull-right">
                <?php echo Former::open('users/bulk')->autocomplete('off')->addClass('mainForm'); ?>

                <div style="display:none">
                    <?php echo Former::text('action'); ?>

                    <?php echo Former::text('public_id')->value($user->public_id); ?>

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
    <?php if($user->showMap()): ?>
        <div id="map">
        </div>
        <br/>
    <?php endif; ?>
    <ul class="nav nav-tabs nav-justified">
        <?php echo Form::tab_link('#activity', trans('texts.activity'), true); ?>

        <?php if(true): ?>
            <?php echo Form::tab_link('#permissions', trans('texts.permissions')); ?>

        <?php endif; ?>
        <?php if(true): ?>
            <?php echo Form::tab_link('#groups', trans('texts.groups')); ?>

        <?php endif; ?>

    </ul>
    <br/>
    <div class="tab-content">
        <?php if(true): ?>
            <div class="tab-pane" id="permissions">
                <?php echo $__env->make('accounts.permission',[
                'permissions' => $permissions,
                'userPermissions' => $userPermissions,
                ], array_except(get_defined_vars(), array('__data', '__path')))->render(); ?>
            </div>
        <?php endif; ?>

        <?php if(true): ?>
            <div class="tab-pane" id="groups">
                <h4>groups</h4>
            </div>
        <?php endif; ?>

    </div>
    <div class="modal fade" id="emailHistoryModal" tabindex="-1" role="dialog"
         aria-labelledby="emailHistoryModalLabel"
         aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
                    <h4 class="modal-title" id="myModalLabel"><?php echo e(trans('texts.email_history')); ?></h4>
                </div>
                <div class="container" style="width: 100%; padding-bottom: 0px !important">
                    <div class="panel panel-default">
                        <div class="panel-body">

                        </div>
                    </div>
                </div>
                <div class="modal-footer" id="signUpFooter" style="margin-top: 0px">
                    <button type="button" class="btn btn-default"
                            data-dismiss="modal"><?php echo e(trans('texts.close')); ?> </button>
                    <button type="button" class="btn btn-danger" onclick="onReactivateClick()" id="reactivateButton"
                            style="display:none;"><?php echo e(trans('texts.reactivate')); ?> </button>
                </div>
            </div>
        </div>
    </div>
    <script type="text/javascript">
        // // Check/Uncheck all radio buttons in the group
        // $('tr.header-row input:radio').on('ifClicked', function () {
        //     value = $(this).attr('value');
        //     alert(value);
        //     area = $(this).data('checker-group');
        //     $('.radiochecker-' + area + '[value=' + value + ']').iCheck('check');
        // });
        //
        // $('.header-name').click(function () {
        //     $(this).parent().nextUntil('tr.header-row').slideToggle(500);
        // });
        //
        // $('.tooltip-base').tooltip({container: 'body'});
        // $(".superuser").change(function () {
        //     var perms = $(this).val();
        //     if (perms == '1') {
        //         $("#nonadmin").hide();
        //     } else {
        //         $("#nonadmin").show();
        //     }
        // });
        var loadedTabs = {};
        $(function () {
            $('.normalDropDown:not(.dropdown-toggle)').click(function (event) {
                openUrlOnClick('<?php echo e(URL::to('users/' . $user->public_id . '/edit')); ?>', event);
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

        function onPurgeClick() {
            sweetConfirm(function () {
                $('#action').val('purge');
                $('.mainForm').submit();
            }, "<?php echo e(trans('texts.purge_user_warning') . "\\n\\n" . trans('texts.mobile_refresh_warning') . "\\n\\n" . trans('texts.no_undo')); ?>");
        }

        function showEmailHistory(email) {
            window.emailBounceId = false;
            $('#emailHistoryModal .panel-body').html("<?php echo e(trans('texts.loading')); ?>...");
            $('#reactivateButton').hide();
            $('#emailHistoryModal').modal('show');
            $.post('<?php echo e(url('/email_history')); ?>', {email: email}, function (data) {
                $('#emailHistoryModal .panel-body').html(data.str);
                window.emailBounceId = data.bounce_id;
                $('#reactivateButton').toggle(!!window.emailBounceId);
            })
        }

        function onReactivateClick() {
            $.post('<?php echo e(url('/reactivate_email')); ?>/' + window.emailBounceId, function (data) {
                $('#emailHistoryModal').modal('hide');
                swal("<?php echo e(trans('texts.reactivated_email')); ?>")
            })
        }

        <?php if($user->showMap()): ?>
        function initialize() {
            var mapCanvas = document.getElementById('map');
            var mapOptions = {
                zoom: <?php echo e(DEFAULT_MAP_ZOOM); ?>,
                mapTypeId: google.maps.MapTypeId.ROADMAP,
                zoomControl: true,
            };

            var map = new google.maps.Map(mapCanvas, mapOptions)
            var address = <?php echo json_encode(e("{$user->address1} {$user->address2} {$user->city} {$user->state} {$user->postal_code} " . ($user->country ? $user->country->getName() : ''))); ?>;

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
        <?php endif; ?>
    </script>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('header', array_except(get_defined_vars(), array('__data', '__path')))->render(); ?>