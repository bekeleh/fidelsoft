<!DOCTYPE html>
<html lang="<?php echo e(App::getLocale()); ?>">
<head>
    <meta charset="utf-8">

    <?php if(!auth()->check()): ?>
        <title><?php echo e(trans('texts.client_portal')); ?></title>
        <link href="<?php echo e(asset('logo.png')); ?>" rel="shortcut icon" type="image/png">
    <?php else: ?>
        <title><?php echo e(isset($title) ? ($title . ' | '. trans('texts.team_source')) : ('Fidelsoft' )); ?></title>
        <meta name="description" content="<?php echo e(isset($description) ? $description : trans('texts.app_description')); ?>"/>
        <link href="<?php echo e(asset('logo.gif')); ?>" rel="shortcut icon" type="image/gif">
        <meta property="og:site_name" content="Fidelsoft"/>
        <meta property="og:url" content="<?php echo e(SITE_URL); ?>"/>
        <meta property="og:title" content="Fidelsoft"/>
        <meta property="og:image" content="<?php echo e(SITE_URL); ?>/images/round_logo.png"/>
        <meta property="og:description" content="Fidelsoft"/>

        <!-- http://realfavicongenerator.net -->
        <link rel="manifest" href="<?php echo e(url('manifest.json')); ?>">
        <link rel="mask-icon" href="<?php echo e(url('safari-pinned-tab.svg')); ?>" color="#3bc65c">
        <link rel="shortcut icon" href="<?php echo e(url('favicon.ico')); ?>">
        <meta name="apple-mobile-web-app-title" content="Fidelsoft">
        <meta name="application-name" content="Fidelsoft">
        <meta name="theme-color" content="#ffffff">
    <?php endif; ?>
    <meta http-equiv="cache-control" content="max-age=0"/>
    <meta http-equiv="cache-control" content="no-cache"/>
    <meta http-equiv="cache-control" content="no-store"/>
    <meta http-equiv="cache-control" content="must-revalidate"/>
    <meta http-equiv="expires" content="0"/>
    <meta http-equiv="expires" content="Tue, 01 Jan 2019 1:00:00 GMT"/>
    <meta http-equiv="pragma" content="no-cache"/>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="<?php echo e(csrf_token()); ?>">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <link rel="canonical" href="<?php echo e(NINJA_APP_URL); ?>/<?php echo e(Request::path()); ?>"/>
    
    <?php echo $__env->yieldContent('head_css'); ?>

    <script src="<?php echo e(asset('built.js')); ?>?no_cache=<?php echo e(NINJA_VERSION); ?>" type="text/javascript">
    </script>


    <script type="text/javascript">
        var NINJA = NINJA || {};
        NINJA.fontSize = 9;
        NINJA.isRegistered = <?php echo e(Utils::isRegistered() ? 'true' : 'false'); ?>;
        NINJA.loggedErrorCount = 0;

        window.onerror = function (errorMsg, url, lineNumber, column, error) {
            if (NINJA.loggedErrorCount > 5) {
                return;
            }
            NINJA.loggedErrorCount++;

// Error in hosted third party library
            if (errorMsg.indexOf('Script error.') > -1) {
                return;
            }
// Error due to incognito mode
            if (errorMsg.indexOf('DOM Exception 22') > -1) {
                return;
            }
            <?php if(Utils::isTravis()): ?>
            if (errorMsg.indexOf('Attempting to change value of a readonly property') > -1) {
                return;
            }
            <?php endif; ?>
            // Less than IE9 https://stackoverflow.com/a/14835682/497368
            if (!document.addEventListener) {
                return;
            }
            try {
// Use StackTraceJS to parse the error context
                if (error) {
                    StackTrace.fromError(error).then(function (result) {
                        var gps = new StackTraceGPS();
                        gps.findFunctionName(result[0]).then(function (result) {
                            logError(errorMsg + ': ' + JSON.stringify(result));
                        });
                    }).catch(function () {
                        logError(errorMsg);
                    });
                } else {
                    logError(errorMsg);
                }

                trackEvent('/error', errorMsg);
            } catch (exception) {
                console.log('Failed to log error');
                console.log(exception);
            }

            return false;
        };

        function logError(message) {
            $.ajax({
                type: 'GET',
                url: '<?php echo e(URL::to('log_error')); ?>',
                data: 'error=' + encodeURIComponent(message) + '&url=' + encodeURIComponent(window.location)
            });
        }

        // http://t4t5.github.io/sweetalert/
        function sweetConfirm(successCallback, text, title, cancelCallback) {
            title = title || <?php echo json_encode(trans("texts.are_you_sure")); ?>;
            swal({
                type: "warning",
                confirmButtonColor: "#DD6B55",
                title: title,
                text: text,
                cancelButtonText: <?php echo json_encode(trans("texts.no")); ?>,
                confirmButtonText: <?php echo json_encode(trans("texts.yes")); ?>,
                showCancelButton: true,
                closeOnConfirm: false,
                allowOutsideClick: true,
            }).then(function () {
                successCallback();
                swal.close();
            }).catch(function () {
                if (cancelCallback) {
                    cancelCallback();
                }
            });
        }

        function showPasswordStrength(password, score) {
            if (password) {
                var str = <?php echo json_encode(trans('texts.password_strength')); ?> +': ';
                if (password.length < 10 || score < 50) {
                    str += <?php echo json_encode(trans('texts.strength_weak')); ?>;
                } else if (score < 75) {
                    str += <?php echo json_encode(trans('texts.strength_good')); ?>;
                } else {
                    str += <?php echo json_encode(trans('texts.strength_strong')); ?>;
                }
                $('#passwordStrength').html(str);
            } else {
                $('#passwordStrength').html('&nbsp;');
            }
        }

        /* Set the defaults for DataTables initialisation */
        $.extend(true, $.fn.dataTable.defaults, {
            "bSortClasses": false,
            "sDom": "t<'row-fluid'<'span6 dt-left'i><'span6 dt-right'p>>l",
            "sPaginationType": "bootstrap",
            "bInfo": true,
            "oLanguage": {
                'sEmptyTable': "<?php echo e(trans('texts.empty_table')); ?>",
                'sInfoEmpty': "<?php echo e(trans('texts.empty_table_footer')); ?>",
                'sLengthMenu': '_MENU_ <?php echo e(trans('texts.rows')); ?>',
                'sInfo': "<?php echo e(trans('texts.datatable_info', ['start' => '_START_', 'end' => '_END_', 'total' => '_TOTAL_'])); ?>",
                'sSearch': ''
            }
        });

        /* This causes problems with some languages. ie, fr_CA
        var appLocale = '<?php echo e(App::getLocale()); ?>';
*/

        <?php if(env('FACEBOOK_PIXEL')): ?>
        <!-- Facebook Pixel Code -->
        !function (f, b, e, v, n, t, s) {
            if (f.fbq) return;
            n = f.fbq = function () {
                n.callMethod ?
                    n.callMethod.apply(n, arguments) : n.queue.push(arguments)
            };
            if (!f._fbq) f._fbq = n;
            n.push = n;
            n.loaded = !0;
            n.version = '2.0';
            n.queue = [];
            t = b.createElement(e);
            t.async = !0;
            t.src = v;
            s = b.getElementsByTagName(e)[0];
            s.parentNode.insertBefore(t, s)
        }(window,
            document, 'script', '//connect.facebook.net/en_US/fbevents.js');

        fbq('init', '<?php echo e(env('FACEBOOK_PIXEL')); ?>');
        fbq('track', "PageView");

        (function () {
            var _fbq = window._fbq || (window._fbq = []);
            if (!_fbq.loaded) {
                var fbds = document.createElement('script');
                fbds.async = true;
                fbds.src = '//connect.facebook.net/en_US/fbds.js';
                var s = document.getElementsByTagName('script')[0];
                s.parentNode.insertBefore(fbds, s);
                _fbq.loaded = true;
            }
        })();

        <?php else: ?>
        function fbq() {
// do nothing
        }

        <?php endif; ?>
            window._fbq = window._fbq || [];
    </script>

    <?php if(!request()->borderless): ?>
        <link rel="stylesheet" type="text/css" href="<?php echo e(asset('css/cookieconsent.min.css')); ?>"/>
        <script src="<?php echo e(asset('js/cookieconsent.min.js')); ?>"></script>
        <script>
            window.addEventListener("load", function () {
                    if (!window.cookieconsent) {
                        return;
                    }
                    window.cookieconsent.initialise({
                        "palette": {
                            "popup": {
                                "background": "#000"
                            },
                            "button": {
                                "background": "#f1d600"
                            },
                        },
                        "content": {
                            "href": "<?php echo e(Utils::isNinja() ? config('ninja.privacy_policy_url.hosted') : 'https://cookiesandyou.com/'); ?>",
                            "message": <?php echo json_encode(trans('texts.cookie_message')); ?>,
                            "dismiss": <?php echo json_encode(trans('texts.got_it')); ?>,
                            "link": <?php echo json_encode(trans('texts.learn_more')); ?>,
                        }
                    })
                }
            );
        </script>
    <?php endif; ?>

<!-- HTML5 shim and Respond.js IE8 support of HTML5 elements and media queries -->
    <!--[if lt IE 9]>
    <script src="https://oss.maxcdn.com/libs/html5shiv/3.7.0/html5shiv.js"></script>
    <script src="https://oss.maxcdn.com/libs/respond.js/1.3.0/respond.min.js"></script>
    <![endif]-->

    <?php echo $__env->yieldContent('head'); ?>

</head>
<body class="body">
<?php if(request()->phantomjs): ?>
    <script>
        function trackEvent(category, action) {
        }
    </script>
<?php elseif(Utils::isNinjaProd() && isset($_ENV['ANALYTICS_KEY']) && $_ENV['ANALYTICS_KEY']): ?>
    <script>
        (function (i, s, o, g, r, a, m) {
            i['GoogleAnalyticsObject'] = r;
            i[r] = i[r] || function () {
                (i[r].q = i[r].q || []).push(arguments)
            }, i[r].l = 1 * new Date();
            a = s.createElement(o),
                m = s.getElementsByTagName(o)[0];
            a.async = 1;
            a.src = g;
            m.parentNode.insertBefore(a, m)
        })(window, document, 'script', '//www.google-analytics.com/analytics.js', 'ga');

        ga('create', '<?php echo e($_ENV['ANALYTICS_KEY']); ?>', 'auto');
        ga('set', 'anonymizeIp', true);

        <?php if(request()->invitation_key || request()->proposal_invitation_key || request()->contact_key): ?>
        ga('send', 'pageview', {'page': '/client/portal'});
        <?php else: ?>
        ga('send', 'pageview');

        <?php endif; ?>

        function trackEvent(category, action) {
            ga('send', 'event', category, action, this.src);
        }
    </script>
<?php else: ?>
    <script>
        function trackEvent(category, action) {
        }
    </script>
<?php endif; ?>

<?php echo $__env->yieldContent('body'); ?>

<center>
    <div class="bottom" style="color: #777 !important;">
        Copyright &copy;<?php echo e(date('Y')); ?>

        <a href="mailto:fidelinvoice@gmail.com" style=";text-decoration: none;">
            <strong><?php echo e(trans('texts.team_source')); ?></strong> </a>. All rights reserved.
    </div>
</center>

<script type="text/javascript">
    NINJA.formIsChanged = <?php echo e(isset($formIsChanged) && $formIsChanged ? 'true' : 'false'); ?>;
    NINJA.parseFloat = function (str) {
        if (!str) {
            return '';
        } else {
            str = str + '';
        }

// check for comma as decimal separator
        if (str.match(/,[\d]{1,2}$/)) {
            str = str.replace(',', '.');
            str = str.replace('.', ',');
        }

        str = str.replace(/[^0-9\.\-]/g, '');

        return window.parseFloat(str);
    };

    $(function () {
        $('form.warn-on-exit input, form.warn-on-exit textarea, form.warn-on-exit select').change(function () {
            NINJA.formIsChanged = true;
        });

        $.ajaxSetup({
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            }
        });

        <?php if(Session::has('trackEventCategory') && Session::has('trackEventAction')): ?>
        <?php if(Session::get('trackEventAction') === '/buy_pro_plan'): ?>
        fbq('track', 'Bill', {value: '<?php echo e(session('trackEventAmount')); ?>', currency: 'USD'});
        <?php endif; ?>
        <?php endif; ?>

        $('[data-toggle="tooltip"]').tooltip();

        <?php if(Session::has('onReady')): ?>
        <?php echo e(Session::get('onReady')); ?>

        <?php endif; ?>
    });
    $('form').submit(function () {
        NINJA.formIsChanged = false;
    });
    $(window).on('beforeunload', function () {
        if (NINJA.formIsChanged) {
            return "<?php echo e(trans('texts.unsaved_changes')); ?>";
        } else {
            return undefined;
        }
    });

    function openUrl(url, track) {
        trackEvent('/view_link', track ? track : url);
        window.open(url, '_blank');
    }
</script>
</body>
</html>
