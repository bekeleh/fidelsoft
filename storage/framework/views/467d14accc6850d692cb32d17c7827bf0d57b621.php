<?php $__env->startSection('head'); ?>
    ##parent-placeholder-1a954628a960aaef81d7b2d4521929579f3541e6##

<?php $__env->stopSection(); ?>

<?php $__env->startSection('content'); ?>
    <!-- edit user -->
    <div class="row">
        <div class="col-md-7">
            <ol class="breadcrumb">
                <li><?php echo e(link_to('/users', trans('texts.users'))); ?></li>
                <li class='active'><?php echo e($user->present()->fullName); ?></li> <?php echo $user->present()->statusLabel; ?>

            </ol>
        </div>
        <div class="col-md-5">
            <div class="pull-right">
                <?php echo Former::open('users/bulk')->autocomplete('off')->addClass('mainForm'); ?>

                <div style="display:none">
                    <?php echo Former::text('action'); ?>

                    <?php echo Former::text('public_id')->value($user->public_id); ?>

                </div>

                <?php if(!$user->is_deleted): ?>
                    <?php if (app(\Illuminate\Contracts\Auth\Access\Gate::class)->check('edit', $user)): ?>
                        <?php echo DropdownButton::normal(trans('texts.edit_user'))
                            ->withAttributes(['class'=>'normalDropDown'])
                            ->withContents([
                              ($user->trashed() ? false : ['label' => trans('texts.archive_user'), 'url' => "javascript:onArchiveClick()"]),
                              ['label' => trans('texts.delete_user'), 'url' => "javascript:onDeleteClick()"],
                              auth()->user()->is_admin ? \DropdownButton::DIVIDER : false,
                              auth()->user()->is_admin ? ['label' => trans('texts.purge_user'), 'url' => "javascript:onPurgeClick()"] : false,
                            ]
                          )->split(); ?>

                    <?php endif; ?>
                <?php endif; ?>
                <?php if($user->trashed()): ?>
                    <?php if (app(\Illuminate\Contracts\Auth\Access\Gate::class)->check('edit', $user)): ?>
                        <?php if(auth()->user()->is_admin && $user->is_deleted): ?>
                            <?php echo Button::danger(trans('texts.purge_user'))
                                    ->appendIcon(Icon::create('warning-sign'))
                                    ->withAttributes(['onclick' => 'onPurgeClick()']); ?>

                        <?php endif; ?>
                        <?php echo Button::primary(trans('texts.restore_user'))
                                ->appendIcon(Icon::create('retweet'))
                                ->withAttributes(['onclick' => 'onRestoreClick()']); ?>

                    <?php endif; ?>
                <?php endif; ?>
                <?php echo Former::close(); ?>

            </div>
        </div>
    </div>
    <!-- user detail -->
    <div class="panel panel-default">
        <div class="panel-body">
            <div class="row">
                <div class="col-md-3">
                    <h3><?php echo e(trans('texts.user_details')); ?></h3>
                    <?php if($user): ?>
                        <p><i class="fa fa-id-number"
                              style="width: 20px"></i><?php echo e(trans('texts.id_number').': '.$user->id); ?></p>
                    <?php endif; ?>
                    <?php if($user->first_name): ?>
                        <p><i class="fa fa-vat-number"
                              style="width: 20px"></i><?php echo e(trans('texts.first_name').': '. $user->present()->fullName); ?>

                        </p>
                    <?php endif; ?>
                    <?php if($user->notes): ?>
                        <p><i><?php echo nl2br(e($user->notes)); ?></i></p>
                    <?php endif; ?>
                    <?php if($user->last_login): ?>
                        <h3 style="margin-top:0px"><small>
                                <?php echo e(trans('texts.last_logged_in')); ?> <?php echo e(Utils::timestampToDateTimeString(strtotime($user->last_login))); ?>

                            </small>
                        </h3>
                    <?php endif; ?>
                </div>
                <div class="col-md-3">
                    <h3><?php echo e(trans('texts.address')); ?></h3>
                    <p>address details</p>
                </div>
                <div class="col-md-3">
                    <h3><?php echo e(trans('texts.contacts')); ?></h3>
                    <?php if($user->email): ?>
                        <i class="fa fa-envelope"
                           style="width: 20px"></i><?php echo HTML::mailto($user->email, $user->email); ?><br/>
                    <?php endif; ?>
                    <?php if($user->phone): ?>
                        <i class="fa fa-phone" style="width: 20px"></i><?php echo e($user->phone); ?><br/>
                    <?php endif; ?>
                    <br/>
                </div>
            </div>
        </div>
    </div>
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
        <?php if(Auth::user()->isSuperUser() || Auth::user()->is_admin): ?>
            <div class="tab-pane" id="permissions">
                <?php echo $__env->make('accounts.permission',[
                'user' => $user,
                'permissions' => $permissions,
                'userPermissions' => $userPermissions,
                ], array_except(get_defined_vars(), array('__data', '__path')))->render(); ?>
            </div>
        <?php endif; ?>
            <?php if(Auth::user()->isSuperUser() || Auth::user()->is_admin): ?>
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

        function onUpdateClick() {
            $('#action').val('update');
            $('.mainForm').submit();
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