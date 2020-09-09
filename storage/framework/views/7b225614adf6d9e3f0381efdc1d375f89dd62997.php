<?php use App\Ninja\Datatables\BillDatatable;
use App\Ninja\Datatables\BillExpenseDatatable;
use App\Ninja\Datatables\BillPaymentDatatable;
use App\Ninja\Datatables\RecurringBillDatatable;
use App\Ninja\Datatables\VendorCreditDatatable;

$__env->startSection('head'); ?>
    ##parent-placeholder-1a954628a960aaef81d7b2d4521929579f3541e6##

    <script src="<?php echo e(asset('js/select2.min.js')); ?>" type="text/javascript"></script>
    <link href="<?php echo e(asset('css/select2.css')); ?>" rel="stylesheet" type="text/css"/>

    <?php if($vendor->showMap()): ?>
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
                <li><?php echo e(link_to('/vendors', trans('texts.vendors'))); ?></li>
                <li class='active'><?php echo e($vendor->getDisplayName()); ?></li> <?php echo $vendor->present()->statusLabel; ?>

            </ol>
        </div>
        <div class="col-md-5">
            <div class="pull-right">
                <?php echo Former::open('vendors/bulk')->autocomplete('off')->addClass('mainForm'); ?>

                <div style="display:none">
                    <?php echo Former::text('action'); ?>

                    <?php echo Former::text('public_id')->value($vendor->public_id); ?>

                </div>

                <?php if($gatewayLink): ?>
                    <?php echo Button::normal(trans('texts.view_in_gateway', ['gateway'=>$gatewayName]))
                    ->asLinkTo($gatewayLink)
                    ->withAttributes(['target' => '_blank']); ?>

                <?php endif; ?>

                <?php if(!$vendor->is_deleted): ?>
                    <?php if (app(\Illuminate\Contracts\Auth\Access\Gate::class)->check('edit', $vendor)): ?>
                        <?php echo DropdownButton::normal(trans('texts.edit_vendor'))
                        ->withAttributes(['class'=>'normalDropDown'])
                        ->withContents([
                        ($vendor->trashed() ? false : ['label' => trans('texts.archive_vendor'), 'url' => "javascript:onArchiveClick()"]),
                        ['label' => trans('texts.delete_vendor'), 'url' => "javascript:onDeleteClick()"],
                        auth()->user()->is_admin ? DropdownButton::DIVIDER : false,
                        ]
                        )->split(); ?>

                        <?php endif; ?>-
                        
                        
                        
                        
                        
                        
                        
                    <?php endif; ?>
                    <?php if($vendor->trashed()): ?>
                        <?php if (app(\Illuminate\Contracts\Auth\Access\Gate::class)->check('edit', $vendor)): ?>
                            <?php echo Button::primary(trans('texts.restore_vendor'))
                            ->appendIcon(Icon::create('retweet'))
                            ->withAttributes(['onclick' => 'onRestoreClick()']); ?>

                        <?php endif; ?>
                    <?php endif; ?>
                    <?php echo Former::close(); ?>

            </div>
        </div>
    </div>
    <?php if($vendor->last_login): ?>
        <h3 style="margin-top:0px"><small>
                <?php echo e(trans('texts.last_logged_in')); ?> <?php echo e(Utils::timestampToDateTimeString(strtotime($vendor->last_login))); ?>

            </small>
        </h3>
    <?php endif; ?>
    <div class="panel panel-default">
        <div class="panel-body">
            <div class="row">
                <div class="col-md-3">
                    <h3><?php echo e(trans('texts.details')); ?></h3>
                    <?php if($vendor->id_number): ?>
                        <p><i class="fa fa-id-number"
                              style="width: 20px"></i><?php echo e(trans('texts.id_number').': '.$vendor->id_number); ?></p>
                    <?php endif; ?>
                    <?php if($vendor->vat_number): ?>
                        <p><i class="fa fa-vat-number"
                              style="width: 20px"></i><?php echo e(trans('texts.vat_number').': '.$vendor->vat_number); ?></p>
                    <?php endif; ?>

                    <?php if($vendor->account->customLabel('vendor1') && $vendor->custom_value1): ?>
                        <?php echo e($vendor->account->present()->customLabel('vendor1') . ': '); ?> <?php echo nl2br(e($vendor->custom_value1)); ?>

                        <br/>
                    <?php endif; ?>
                    <?php if($vendor->account->customLabel('vendor2') && $vendor->custom_value2): ?>
                        <?php echo e($vendor->account->present()->customLabel('vendor2') . ': '); ?> <?php echo nl2br(e($vendor->custom_value2)); ?>

                        <br/>
                    <?php endif; ?>

                    <?php if($vendor->work_phone): ?>
                        <i class="fa fa-phone" style="width: 20px"></i><?php echo e($vendor->work_phone); ?>

                    <?php endif; ?>

                    <?php if(floatval($vendor->task_rate)): ?>
                        <p><?php echo e(trans('texts.task_rate')); ?>: <?php echo e(Utils::roundSignificant($vendor->task_rate)); ?></p>
                    <?php endif; ?>
                    <?php if($vendor->public_notes): ?>
                        <p><i><?php echo nl2br(e($vendor->public_notes)); ?></i></p>
                    <?php endif; ?>
                    <?php if($vendor->private_notes): ?>
                        <p><i><?php echo nl2br(e($vendor->private_notes)); ?></i></p>
                    <?php endif; ?>
                    <?php if($vendor->industry || $vendor->size): ?>
                        <?php if($vendor->industry): ?>
                            <?php echo e($vendor->industry->name); ?>

                        <?php endif; ?>
                        <?php if($vendor->industry && $vendor->size): ?>
                            |
                        <?php endif; ?>
                        <?php if($vendor->size): ?>
                            <?php echo e($vendor->size->name); ?><br/>
                        <?php endif; ?>
                    <?php endif; ?>
                    <?php if($vendor->website): ?>
                        <p><?php echo Utils::formatWebsite($vendor->website); ?></p>
                    <?php endif; ?>
                    <?php if($vendor->language): ?>
                        <p><i class="fa fa-language" style="width: 20px"></i><?php echo e($vendor->language->name); ?></p>
                    <?php endif; ?>
                    <p><?php echo e(trans('texts.payment_terms').': '.trans('texts.payment_terms_net')); ?> <?php echo e($vendor->present()->paymentTerms); ?></p>
                    <!--- vendor vendor type -->
                    <p><?php echo e(trans('texts.vendor_type_name').': '); ?> <?php echo e($vendor->present()->vendorType); ?></p>
                    <!--- vendor hold reason -->
                    <p><?php echo e(trans('texts.hold_reason_name').': '); ?><?php echo e($vendor->present()->holdReason); ?></p>
                    <div class="text-muted" style="padding-top:8px">
                        <?php if($vendor->show_tasks_in_portal): ?>
                            • <?php echo e(trans('texts.can_view_tasks')); ?><br/>
                        <?php endif; ?>
                        <?php if($vendor->account->hasReminders() && ! $vendor->send_reminders): ?>
                            • <?php echo e(trans('texts.is_not_sent_reminders')); ?></br>
                        <?php endif; ?>
                    </div>
                </div>
                <div class="col-md-3">
                    <h3><?php echo e(trans('texts.address')); ?></h3>
                    <?php if($vendor->addressesMatch()): ?>
                        <?php echo $vendor->present()->address(ADDRESS_BILLING); ?>

                    <?php else: ?>
                        <?php echo $vendor->present()->address(ADDRESS_BILLING, true); ?><br/>
                        <?php echo $vendor->present()->address(ADDRESS_SHIPPING, true); ?>

                    <?php endif; ?>
                </div>

                <div class="col-md-3">
                    <h3><?php echo e(trans('texts.vendor_contacts')); ?></h3>
                    <?php $__currentLoopData = $vendor->contacts; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $contact): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                        <?php if($contact->first_name || $contact->last_name): ?>
                            <b><?php echo e($contact->first_name.' '.$contact->last_name); ?></b><br/>
                        <?php endif; ?>
                        <?php if($contact->email): ?>
                            <i class="fa fa-envelope"
                               style="width: 20px"></i><?php echo HTML::mailto($contact->email, $contact->email); ?><br/>
                        <?php endif; ?>
                        <?php if($contact->phone): ?>
                            <i class="fa fa-phone" style="width: 20px"></i><?php echo e($contact->phone); ?><br/>
                        <?php endif; ?>

                        <?php if($vendor->account->customLabel('contact1') && $contact->custom_value1): ?>
                            <?php echo e($vendor->account->present()->customLabel('contact1') . ': ' . $contact->custom_value1); ?>

                            <br/>
                        <?php endif; ?>
                        <?php if($vendor->account->customLabel('contact2') && $contact->custom_value2): ?>
                            <?php echo e($vendor->account->present()->customLabel('contact2') . ': ' . $contact->custom_value2); ?>

                            <br/>
                        <?php endif; ?>

                        <?php if(Auth::user()->confirmed && $vendor->account->enable_vendor_portal): ?>
                            <i class="fa fa-dashboard" style="width: 20px"></i>
                            <a href="<?php echo e($contact->link); ?>"
                               onclick="window.open('<?php echo e($contact->link); ?>?silent=true', '_blank');return false;">
                                <?php echo e(trans('texts.view_in_portal')); ?>

                            </a>
                            <?php if(config('services.postmark')): ?>
                                | <a href="#" onclick="showEmailHistory('<?php echo e($contact->email); ?>')">
                                    <?php echo e(trans('texts.email_history')); ?>

                                </a>
                            <?php endif; ?>
                            <br/>
                        <?php endif; ?>
                        <br/>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                </div>

                <div class="col-md-3">
                    <h3><?php echo e(trans('texts.standing')); ?>

                        <table class="table" style="width:100%">
                            <tr>
                                <td><small><?php echo e(trans('texts.paid_to_date')); ?></small></td>
                                <td style="text-align: left"><?php echo e(Utils::formatMoney($vendor->paid_to_date, $vendor->getCurrencyId())); ?>

                                </td>
                            </tr>
                            <tr>
                                <td><small><?php echo e(trans('texts.balance')); ?></small></td>
                                <td style="text-align: left"><?php echo e(Utils::formatMoney($vendor->balance, $vendor->getCurrencyId())); ?>

                                </td>
                            </tr>
                            <?php if($credit > 0): ?>
                                <tr>
                                    <td><small><?php echo e(trans('texts.vendor_credit')); ?></small></td>
                                    <td style="text-align: left"><?php echo e(Utils::formatMoney($credit, $vendor->getCurrencyId())); ?>

                                    </td>
                                </tr>
                            <?php endif; ?>
                        </table>
                    </h3>
                </div>
            </div>
        </div>
    </div>

    <?php if($vendor->showMap()): ?>
        <div id="map"></div>
        <br/>
    <?php endif; ?>

    <ul class="nav nav-tabs nav-justified">
        <?php echo Form::tab_link('#activity', trans('texts.activity'), true); ?>

        <?php if($hasExpenses): ?>
            <?php echo Form::tab_link('#expenses', trans('texts.expenses')); ?>

        <?php endif; ?>
        <?php if($hasQuotes): ?>
            <?php echo Form::tab_link('#quotes', trans('texts.quotes')); ?>

        <?php endif; ?>
        <?php if($hasRecurringInvoices): ?>
            <?php echo Form::tab_link('#recurring_bills', trans('texts.recurring')); ?>

        <?php endif; ?>
        <?php echo Form::tab_link('#bills', trans('texts.bills')); ?>

        <?php echo Form::tab_link('#payments', trans('texts.bill_payments')); ?>

        <?php if($account->isModuleEnabled(ENTITY_VENDOR_CREDIT)): ?>
            <?php echo Form::tab_link('#credits', trans('texts.vendor_credit')); ?>

        <?php endif; ?>
    </ul>
    <br/>

    <div class="tab-content">
        <div class="tab-pane active" id="activity">
            <?php echo Datatable::table()
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
                ->render('datatable'); ?>

        </div>

        <?php if($hasExpenses): ?>
            <div class="tab-pane" id="expenses">
                <?php echo $__env->make('list', [
                'entityType' => ENTITY_BILL_EXPENSE,
                'datatable' => new BillExpenseDatatable(true, true),
                'vendorId' => $vendor->public_id,
                'url' => url('api/vendor_expenses/' . $vendor->public_id),
                ], array_except(get_defined_vars(), array('__data', '__path')))->render(); ?>
            </div>
        <?php endif; ?>

        <?php if(Utils::hasFeature(FEATURE_QUOTES) && $hasQuotes): ?>
            <div class="tab-pane" id="quotes">
                <?php echo $__env->make('list', [
                'entityType' => ENTITY_BILL_QUOTE,
                'datatable' => new BillDatatable(true, true, ENTITY_BILL_QUOTE),
                'vendorId' => $vendor->public_id,
                'url' => url('api/bill_quotes/' . $vendor->public_id),
                ], array_except(get_defined_vars(), array('__data', '__path')))->render(); ?>
            </div>
        <?php endif; ?>

        <?php if($hasRecurringInvoices): ?>
            <div class="tab-pane" id="recurring_bills">
                <?php echo $__env->make('list', [
                'entityType' => ENTITY_RECURRING_BILL,
                'datatable' => new RecurringBillDatatable(true, true),
                'vendorId' => $vendor->public_id,
                'url' => url('api/recurring_bills/' . $vendor->public_id),
                ], array_except(get_defined_vars(), array('__data', '__path')))->render(); ?>
            </div>
        <?php endif; ?>

        <div class="tab-pane" id="bills">
            <?php echo $__env->make('list', [
            'entityType' => ENTITY_BILL,
            'datatable' => new BillDatatable(true, true),
            'vendorId' => $vendor->public_id,
            'url' => url('api/bills/' . $vendor->public_id),
            ], array_except(get_defined_vars(), array('__data', '__path')))->render(); ?>
        </div>

        <div class="tab-pane" id="payments">
            <?php echo $__env->make('list', [
            'entityType' => ENTITY_BILL_PAYMENT,
            'datatable' => new BillPaymentDatatable(true, true),
            'vendorId' => $vendor->public_id,
            'url' => url('api/bill_payments/' . $vendor->public_id),
            ], array_except(get_defined_vars(), array('__data', '__path')))->render(); ?>
        </div>

        <?php if($account->isModuleEnabled(ENTITY_VENDOR_CREDIT)): ?>
            <div class="tab-pane" id="credits">
                <?php echo $__env->make('list', [
                'entityType' => ENTITY_VENDOR_CREDIT,
                'datatable' => new VendorCreditDatatable(true, true),
                'vendorId' => $vendor->public_id,
                'url' => url('api/vendor_credits/' . $vendor->public_id),
                ], array_except(get_defined_vars(), array('__data', '__path')))->render(); ?>
            </div>
        <?php endif; ?>
    </div>
    <div class="modal fade" id="emailHistoryModal" tabindex="-1" role="dialog" aria-labelledby="emailHistoryModalLabel"
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
                openUrlOnClick('<?php echo e(URL::to('vendors/' . $vendor->public_id . '/edit')); ?>', event);
            });
            $('.primaryDropDown:not(.dropdown-toggle)').click(function (event) {
                openUrlOnClick('<?php echo e(URL::to('vendors/statement/' . $vendor->public_id )); ?>', event);
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
            }, "<?php echo e(trans('texts.purge_vendor_warning') . "\\n\\n" . trans('texts.mobile_refresh_warning') . "\\n\\n" . trans('texts.no_undo')); ?>");
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

        <?php if($vendor->showMap()): ?>
        function initialize() {
            var mapCanvas = document.getElementById('map');
            var mapOptions = {
                zoom: <?php echo e(DEFAULT_MAP_ZOOM); ?>,
                mapTypeId: google.maps.MapTypeId.ROADMAP,
                zoomControl: true,
            };

            var map = new google.maps.Map(mapCanvas, mapOptions);
            var address = <?php echo json_encode(e("{$vendor->address1} {$vendor->address2} {$vendor->city} {$vendor->state} {$vendor->postal_code} " . ($vendor->country ? $vendor->country->getName() : ''))); ?>;

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