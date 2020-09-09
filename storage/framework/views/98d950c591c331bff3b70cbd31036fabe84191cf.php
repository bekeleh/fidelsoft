<?php use App\Libraries\HistoryUtils;

$__env->startSection('head_css'); ?>
    <link href="<?php echo e(asset('css/built.css')); ?>?no_cache=<?php echo e(NINJA_VERSION); ?>" rel="stylesheet" type="text/css"/>
<?php $__env->stopSection(); ?>

<?php $__env->startSection('head'); ?>
    <script type="text/javascript">
        function checkForEnter(event) {
            if (event.keyCode === 13) {
                event.preventDefault();
                validateServerSignUp();
                return false;
            }
        }

        function logout(force) {
            if (force) {
                NINJA.formIsChanged = false;
            }
            if (force || NINJA.isRegistered) {
                window.location = '<?php echo e(URL::to('logout')); ?>' + (force ? '?force_logout=true' : '');
            } else {
                $('#logoutModal').modal('show');
            }
        }

        function hideMessage() {
            $('.alert-info').fadeOut();
            $.get('/hide_message', function (response) {
                console.log('Reponse: %s', response);
            });
        }

        function openTimeTracker() {
            var width = 1060;
            var height = 700;
            var left = (screen.width / 2) - (width / 4);
            var top = (screen.height / 2) - (height / 1.5);
            window.open("<?php echo e(url('/time_tracker')); ?>", "time-tracker", "width=" + width + ",height=" + height + ",scrollbars=no,toolbar=no,screenx=" + left + ",screeny=" + top + ",location=no,titlebar=no,directories=no,status=no,menubar=no");
        }

        window.loadedSearchData = false;

        function onSearchBlur() {
            $('#search').typeahead('val', '');
        }

        function onSearchFocus() {
            $('#search-form').show();

            if (!window.loadedSearchData) {
                window.loadedSearchData = true;
                trackEvent('/activity', '/search');
                var request = $.get('<?php echo e(URL::route('get_search_data')); ?>', function (data) {
                    $('#search').typeahead({
                            hint: true,
                            highlight: true,
                        }
                            <?php if(Auth::check() && Auth::user()->account->customLabel('client1')): ?>
                        , {
                            name: 'data',
                            limit: 3,
                            display: 'value',
                            source: searchData(data['<?php echo e(Auth::user()->account->present()->customLabel('client1')); ?>'], 'tokens'),
                            templates: {
                                header: '&nbsp;<span style="font-weight:600;font-size:16px"><?php echo e(Auth::user()->account->present()->customLabel('client1')); ?></span>'
                            }
                        }
                            <?php endif; ?>
                            <?php if(Auth::check() && Auth::user()->account->customLabel('client2')): ?>
                        , {
                            name: 'data',
                            limit: 3,
                            display: 'value',
                            source: searchData(data['<?php echo e(Auth::user()->account->present()->customLabel('client2')); ?>'], 'tokens'),
                            templates: {
                                header: '&nbsp;<span style="font-weight:600;font-size:16px"><?php echo e(Auth::user()->account->present()->customLabel('client2')); ?></span>'
                            }
                        }
                            <?php endif; ?>
                            <?php if(Auth::check() && Auth::user()->account->customLabel('invoice_text1')): ?>
                        , {
                            name: 'data',
                            limit: 3,
                            display: 'value',
                            source: searchData(data['<?php echo e(Auth::user()->account->present()->customLabel('invoice_text1')); ?>'], 'tokens'),
                            templates: {
                                header: '&nbsp;<span style="font-weight:600;font-size:16px"><?php echo e(Auth::user()->account->present()->customLabel('invoice_text1')); ?></span>'
                            }
                        }
                            <?php endif; ?>
                            <?php if(Auth::check() && Auth::user()->account->customLabel('invoice_text2')): ?>
                        , {
                            name: 'data',
                            limit: 3,
                            display: 'value',
                            source: searchData(data['<?php echo e(Auth::user()->account->present()->customLabel('invoice_text2')); ?>'], 'tokens'),
                            templates: {
                                header: '&nbsp;<span style="font-weight:600;font-size:16px"><?php echo e(Auth::user()->account->present()->customLabel('invoice_text2')); ?></span>'
                            }
                        }
                            <?php endif; ?>
                            <?php $__currentLoopData = ['clients', 'contacts', 'invoices', 'quotes', 'navigation']; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $type): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                        , {
                            name: 'data',
                            limit: 3,
                            display: 'value',
                            source: searchData(data['<?php echo e($type); ?>'], 'tokens', true),
                            templates: {
                                header: '&nbsp;<span style="font-weight:600;font-size:16px"><?php echo e(trans("texts.{$type}")); ?></span>'
                            }
                        }
                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                            ).on('typeahead:selected', function (element, datum, name) {
                            window.location = datum.url;
                        }).focus();
                });

                request.error(function (httpObj, textStatus) {
// if the session has expired show login page
                    if (httpObj.status == 401) {
                        location.reload();
                    }
                });
            }
        }

        $(function () {
// auto-logout after 2 hours
            window.setTimeout(function () {
                window.location = '<?php echo e(URL::to('/logout?reason=inactive')); ?>';
            }, <?php echo e(1000 * env('AUTO_LOGOUT_SECONDS', (60 * 60 * 2))); ?>);

// auto-hide status alerts
            window.setTimeout(function () {
                $(".alert-hide").fadeOut();
            }, 3000);

            /* Set the defaults for Bootstrap datepicker */
            $.extend(true, $.fn.datepicker.defaults, {
//language: '<?php echo e($appLanguage); ?>', // causes problems with some languages (ie, fr_CA) if the date includes strings (ie, July 31, 2016)
                weekStart: <?php echo e(Session::get('start_of_week')); ?>

            });

            if (isStorageSupported()) {
                <?php if(Auth::check() && !Auth::user()->registered): ?>
                localStorage.setItem('guest_key', '<?php echo e(Auth::user()->password); ?>');
                <?php endif; ?>
            }

            $('ul.navbar-settings, ul.navbar-search').hover(function () {
                if ($('.user-accounts').css('display') == 'block') {
                    $('.user-accounts').dropdown('toggle');
                }
            });

            <?php echo $__env->yieldContent('onReady'); ?>

            <?php if(Input::has('focus')): ?>
            $('#<?php echo e(Input::get('focus')); ?>').focus();
            <?php endif; ?>

            // Focus the search input if the user clicks forward slash
            $('#search').focusin(onSearchFocus);
            $('#search').blur(onSearchBlur);

// manage sidebar state
            function setupSidebar(side) {
                $("#" + side + "-menu-toggle").click(function (e) {
                    e.preventDefault();
                    $("#wrapper").toggleClass("toggled-" + side);

                    var toggled = $("#wrapper").hasClass("toggled-" + side) ? '1' : '0';
                    $.post('<?php echo e(url('save_sidebar_state')); ?>?show_' + side + '=' + toggled);

                    if (isStorageSupported()) {
                        localStorage.setItem('show_' + side + '_sidebar', toggled);
                    }
                });

                if (isStorageSupported()) {
                    var storage = localStorage.getItem('show_' + side + '_sidebar') || '0';
                    var toggled = $("#wrapper").hasClass("toggled-" + side) ? '1' : '0';

                    if (storage != toggled) {
                        setTimeout(function () {
                            $("#wrapper").toggleClass("toggled-" + side);
                            $.post('<?php echo e(url('save_sidebar_state')); ?>?show_' + side + '=' + storage);
                        }, 200);
                    }
                }
            }

            <?php if( ! Utils::isTravis()): ?>
            setupSidebar('left');
            setupSidebar('right');
            <?php endif; ?>

            // auto select focused nav-tab
            if (window.location.hash) {
                setTimeout(function () {
                    $('.nav-tabs a[href="' + window.location.hash + '"]').tab('show');
                }, 1);
            }

            $('a[data-toggle="tab"]').on('shown.bs.tab', function (e) {
                if (isStorageSupported() && /\/settings\//.test(location.href)) {
                    var target = $(e.target).attr("href"); // activated tab
                    if (history.pushState) {
                        history.pushState(null, null, target);
                    }
                    if (isStorageSupported()) {
                        localStorage.setItem('last:settings_page', location.href.replace(location.hash, ''));
                    }
                }
            });

// set timeout onDomReady
            setTimeout(delayedFragmentTargetOffset, 500);

// add scroll offset to fragment target (if there is one)
            function delayedFragmentTargetOffset() {
                var offset = $(':target').offset();
                if (offset) {
                    var scrollto = offset.top - 180; // minus fixed header height
                    $('html, body').animate({scrollTop: scrollto}, 0);
                }
            }
        });
    </script>
<?php $__env->stopSection(); ?>
<?php $__env->startSection('body'); ?>
    <nav class="navbar navbar-default navbar-fixed-top" role="navigation" style="height:60px;">
        <div class="navbar-header">
            <button type="button" class="navbar-toggle" data-toggle="collapse" data-target="#navbar-collapse-1">
                <span class="sr-only">Toggle navigation</span>
                <span class="icon-bar"></span>
                <span class="icon-bar"></span>
                <span class="icon-bar"></span>
            </button>
            <a href="#" id="left-menu-toggle" class="menu-toggle" title="<?php echo e(trans('texts.toggle_navigation')); ?>"
               style="color:white;">
                <div class="navbar-brand">
                    <i class="fa fa-bars hide-phone" style="width:32px;padding-top:2px;float:left"></i>
                    <img src="<?php echo e(asset('images/round_logo.png')); ?>" width="25" height="25" style="float:left"/>
                    <?php echo e(trans('texts.team_source')); ?>

                </div>
            </a>
        </div>
        <a id="right-menu-toggle" class="menu-toggle hide-phone pull-right"
           title="<?php echo e(trans('texts.toggle_history')); ?>" style="cursor:pointer">
            <div class="fa fa-bars"></div>
        </a>
        <div class="collapse navbar-collapse" id="navbar-collapse-1">
            <div class="navbar-form navbar-right">
                <?php echo $__env->make('partials.notification', array_except(get_defined_vars(), array('__data', '__path')))->render(); ?>
                
                <?php echo $__env->make('partials.sidebar_auth', array_except(get_defined_vars(), array('__data', '__path')))->render(); ?>
            </div>
            <?php echo Former::open('/handle_command')->id('search-form')->addClass('navbar-form navbar-right')->role('search'); ?>

            <div class="form-group has-feedback">
                <input type="text" name="command" id="search"
                       style="width: 320px;padding-top:0px;padding-bottom:0px;margin-right:20px;"
                       class="form-control"
                       placeholder="<?php echo e(trans('texts.search')); ?>"/>
                <?php if(env('SPEECH_ENABLED')): ?>
                    <?php echo $__env->make('partials/speech_recognition', array_except(get_defined_vars(), array('__data', '__path')))->render(); ?>
                <?php endif; ?>
            </div>

            <?php echo Former::close(); ?>

            <ul class="nav navbar-nav hide-non-phone" style="font-weight: bold">
                <?php $__currentLoopData = [
                    'dashboard' => false,
                    'users' => false,
                    'permission_groups' => false,
                    'clients' => false,
                    'products' => false,
                    'locations' => false,
                    'bills' => false,
                    'bill_quotes' => false,
                    'bill_orders' => false,
                    'vendor_credits' => false,
                    'bill_payments' => false,
                    'recurring_bills' => false,
                    'invoices' => false,
                    'quotes' => false,
                    'payments' => false,
                    'recurring_invoices' => false,
                    'credits' => false,
                    'proposals' => false,
                    'projects' => false,
                    'tasks' => false,
                    'expenses' => false,
                    'vendors' => false,
                    'manufacturers' => false,
                    'schedules' => false,
                    'reports' => false,
                    'settings' => false,
                    ]; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $key => $value): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                    <?php echo Form::nav_link($key, $value ?: $key); ?>

                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
            </ul>
        </div><!-- /.navbar-collapse -->
    </nav>
    <div id="wrapper"
         class='<?php echo e(session(SESSION_LEFT_SIDEBAR) ? 'toggled-left' : ''); ?> <?php echo e(session(SESSION_RIGHT_SIDEBAR, true) ? 'toggled-right' : ''); ?>'>
        <!-- Sidebar -->
        <div id="left-sidebar-wrapper" class="hide-phone">
            <ul class="sidebar-nav <?php echo e(Auth::user()->dark_mode ? 'sidebar-nav-dark' : 'sidebar-nav-light'); ?>">
            <?php $__currentLoopData = [
                'dashboard',
                'clients',
                'vendors',
                'users',
                'invoices',
                 'quotes',
                'recurring_invoices' => 'recurring',
                'payments',
                'credits',
                 'bills',
                'bill_quotes',
                'recurring_bills',
                'vendor_credits',
                'bill_payments',
                'expenses',
                'products',
                'proposals',
                'projects',
                'tasks',
                'schedules',
                'manufacturers',
                ]; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $option): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                <?php if(!Auth::user()->account->isModuleEnabled(substr($option, 0, -1))): ?>
                    <?php echo e(''); ?>

                <?php else: ?>
                    <?php if(Auth::check() ||Utils::isAdmin() || Auth::user()->can('view', substr($option, 0, -1))): ?>
                        <?php echo $__env->make('partials.navigation_option', array_except(get_defined_vars(), array('__data', '__path')))->render(); ?>
                    <?php endif; ?>
                <?php endif; ?>
            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
            <!-- if user is administrator -->
                <?php if(Utils::isAdmin() || Auth::user()->canCreateOrEdit('view',[ENTITY_REPORT])): ?>
                    <?php echo $__env->make('partials.navigation_option', ['option' => 'reports'], array_except(get_defined_vars(), array('__data', '__path')))->render(); ?>
                <?php endif; ?>
                <?php if(Utils::isAdmin() ): ?>
                    <?php echo $__env->make('partials.navigation_option', ['option' => 'settings'], array_except(get_defined_vars(), array('__data', '__path')))->render(); ?>
                <?php endif; ?>
                <h3 style="height: 50px;"></h3>
            </ul><!-- /.. ul -->
        </div>
        <!-- /#left-sidebar-wrapper -->
        <div id="right-sidebar-wrapper" class="hide-phone" style="overflow-y:hidden">
            <ul class="sidebar-nav <?php echo e(Auth::user()->dark_mode ? 'sidebar-nav-dark' : 'sidebar-nav-light'); ?>">
                <?php echo HistoryUtils::renderHtml(Auth::user()->account_id); ?>

            </ul>
        </div>
        <!-- Page Content -->
        <div id="page-content-wrapper">
            <div class="container-fluid">
                <!-- New message feed area -->
                <?php echo $__env->make('partials.warn_session', ['redirectTo' => '/dashboard'], array_except(get_defined_vars(), array('__data', '__path')))->render(); ?>

                <?php if(Session::has('warning')): ?>
                    <div class="alert alert-warning"><?php echo Session::get('warning'); ?></div>
                <?php elseif(env('WARNING_MESSAGE')): ?>
                    <div class="alert alert-warning"><?php echo env('WARNING_MESSAGE'); ?></div>
                <?php endif; ?>

                <?php if(Session::has('message')): ?>
                    <div class="alert alert-success alert-hide" style="z-index:9999">
                        <?php echo Session::get('message'); ?>

                    </div>
                <?php elseif(Session::has('success')): ?>
                    <div class="alert alert-success alert-hide" style="z-index:9999">
                        <?php echo Session::get('success'); ?>

                    </div>
                <?php elseif(Session::has('news_feed_message')): ?>
                    <div class="alert alert-info">
                        <?php echo Session::get('news_feed_message'); ?>

                        <a href="#" onclick="hideMessage()" class="pull-right"><?php echo e(trans('texts.hide')); ?></a>
                    </div>
                <?php endif; ?>

                <?php if(Session::has('error')): ?>
                    <div class="alert alert-danger"><?php echo Session::get('error'); ?></div>
                <?php endif; ?>
                <div class="pull-right">
                    <?php echo $__env->yieldContent('top-right'); ?>
                </div>

            <?php if(!isset($showBreadcrumbs) || $showBreadcrumbs): ?>
                <?php echo Form::breadcrumbs((! empty($entity) && $entity->exists && !$entity->deleted_at) ? $entity->present()->statusLabel : false); ?>

            <?php endif; ?>
            <!-- Notification area -->
                <!-- Body Content  -->
                <div class="row">
                    <div class="col-md-12">
                        <?php echo $__env->yieldContent('content'); ?>
                    </div>
                </div>
                <br/>
                <!-- Footer  -->
                <div class="row">
                    <div class="col-md-12">
                        <!-- Prod  -->
                    <?php if(Utils::isNinjaProd()): ?>
                        <?php if(Auth::check() && Auth::user()->hasActivePromo()): ?>
                        <?php elseif(Auth::check() && Auth::user()->isTrial()): ?>
                            <?php echo trans(Auth::user()->account->getCountTrialDaysLeft() == 0 ? 'texts.trial_footer_last_day' : 'texts.trial_footer', [
                                'count' => Auth::user()->account->getCountTrialDaysLeft(),
                                ]); ?>

                        <?php endif; ?>
                    <?php else: ?>
                        <!-- In Dev  -->
                            <?php echo $__env->make('partials.white_label', ['company' => Auth::user()->account->company], array_except(get_defined_vars(), array('__data', '__path')))->render(); ?>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
            <!-- /. #page-content-wrapper -->
        </div>
        <?php echo $__env->make('partials.contact_us', array_except(get_defined_vars(), array('__data', '__path')))->render(); ?>
        <?php echo $__env->make('partials.sign_up', array_except(get_defined_vars(), array('__data', '__path')))->render(); ?>
        <?php echo $__env->make('partials.keyboard_shortcuts', array_except(get_defined_vars(), array('__data', '__path')))->render(); ?>
        <?php if(auth()->check() && auth()->user()->registered && !auth()->user()->hasAcceptedLatestTerms()): ?>
            <?php echo $__env->make('partials.accept_terms', array_except(get_defined_vars(), array('__data', '__path')))->render(); ?>
        <?php endif; ?>
    </div>
    <p>&nbsp;</p>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('master', array_except(get_defined_vars(), array('__data', '__path')))->render(); ?>