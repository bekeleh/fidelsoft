<?php $__env->startSection('head'); ?>
    <?php if(!empty($clientFontUrl)): ?>
        <link href="<?php echo e($clientFontUrl); ?>" rel="stylesheet" type="text/css">
    <?php endif; ?>
    <link href="<?php echo e(asset('css/built.public.css')); ?>?no_cache=<?php echo e(NINJA_VERSION); ?>" rel="stylesheet" type="text/css"/>
    <style type="text/css"><?php echo !empty($account)?$account->clientViewCSS():''; ?></style>
<?php $__env->stopSection(); ?>
<?php $__env->startSection('body'); ?>
    <?php echo Form::open(array('url' => 'get_started', 'id' => 'startForm')); ?>

    <?php echo Form::hidden('guest_key'); ?>

    <?php echo Form::hidden('sign_up', Input::get('sign_up')); ?>

    <?php echo Form::hidden('redirect_to', Input::get('redirect_to')); ?>

    <?php echo Form::close(); ?>

    <script>
        if (isStorageSupported()) {
            $('[name="guest_key"]').val(localStorage.getItem('guest_key'));
        }

        function isStorageSupported() {
            if ('localStorage' in window && window['localStorage'] !== null) {
                var storage = window.localStorage;
            } else {
                return false;
            }
            var testKey = 'test';
            try {
                storage.setItem(testKey, '1');
                storage.removeItem(testKey);
                return true;
            } catch (error) {
                return false;
            }
        }

        function getStarted() {
            $('#startForm').submit();
            return false;
        }

        $(function () {
            function positionFooter() {
                // check that the footer appears at the bottom of the screen
                var height = $(window).height() - ($('#header').height() + $('#footer').height());
                if ($('#mainContent').height() < height) {
                    $('#mainContent').css('min-height', height);
                }
            }

            if (inIframe()) {
                $('#footer').hide();
            } else {
                positionFooter();
                $(window).resize(positionFooter);
            }
        })
    </script>
    <div id="header">
        <nav class="navbar navbar-top navbar-inverse">
            <div class="container">
                <div class="navbar-header">
                    <button type="button" class="navbar-toggle collapsed" data-toggle="collapse" data-target="#navbar"
                            aria-expanded="false" aria-controls="navbar">
                        <span class="sr-only">Toggle navigation</span>
                        <span class="icon-bar"></span>
                        <span class="icon-bar"></span>
                        <span class="icon-bar"></span>
                    </button>
                    <?php if(empty($account) || !$account->hasFeature(FEATURE_WHITE_LABEL)): ?>
                        <a class="navbar-brand" href="<?php echo e(URL::to(NINJA_WEB_URL)); ?>" target="_blank">
                            
                        </a>
                    <?php endif; ?>
                </div>
                <div id="navbar" class="collapse navbar-collapse">
                    <ul class="nav navbar-nav navbar-right">
                        <?php if(! empty($account) && $account->enable_client_portal): ?>
                            <?php if(isset($account) && $account->enable_client_portal_dashboard): ?>
                                <li <?php echo Request::is('*client/dashboard*') ? 'class="active"' : ''; ?>>
                                    <?php echo link_to('/client/dashboard', trans('texts.dashboard') ); ?>

                                </li>
                            <?php endif; ?>
                            <?php if(request()->contact && request()->contact->client->show_tasks_in_portal): ?>
                                <li <?php echo Request::is('*client/tasks') ? 'class="active"' : ''; ?>>
                                    <?php echo link_to('/client/tasks', trans('texts.tasks') ); ?>

                                </li>
                            <?php endif; ?>
                            <?php if(isset($hasQuotes) && $hasQuotes): ?>
                                <li <?php echo Request::is('*client/quotes') ? 'class="active"' : ''; ?>>
                                    <?php echo link_to('/client/quotes', trans('texts.quotes') ); ?>

                                </li>
                            <?php endif; ?>
                            <li <?php echo Request::is('*client/invoices') ? 'class="active"' : ''; ?>>
                                <?php echo link_to('/client/invoices', trans('texts.invoices') ); ?>

                            </li>
                            <?php if(!empty($account)
                                && $account->hasFeature(FEATURE_DOCUMENTS)
                                && (isset($hasDocuments) && $hasDocuments)): ?>
                                <li <?php echo Request::is('*client/documents') ? 'class="active"' : ''; ?>>
                                    <?php echo link_to('/client/documents', trans('texts.documents') ); ?>

                                </li>
                            <?php endif; ?>
                            <li <?php echo Request::is('*client/payments') ? 'class="active"' : ''; ?>>
                                <?php echo link_to('/client/payments', trans('texts.payments') ); ?>

                            </li>
                            <?php if(isset($hasCredits) && $hasCredits): ?>
                                <li <?php echo Request::is('*client/credits') ? 'class="active"' : ''; ?>>
                                    <?php echo link_to('/client/credits', trans('texts.credits') ); ?>

                                </li>
                            <?php endif; ?>
                            <?php if(isset($hasPaymentMethods) && $hasPaymentMethods): ?>
                                <li <?php echo Request::is('*client/payment_methods') ? 'class="active"' : ''; ?>>
                                    <?php echo link_to('/client/payment_methods', trans('texts.payment_methods') ); ?>

                                </li>
                            <?php endif; ?>
                            <?php if($account->enable_portal_password && request()->contact->password): ?>
                                <li>
                                    <?php echo link_to('/client/logout', trans('texts.logout')); ?>

                                </li>
                            <?php endif; ?>
                        <?php elseif(! empty($account)): ?>
                            <?php if(isset($hasPaymentMethods) && $hasPaymentMethods): ?>
                                <li <?php echo Request::is('*client/payment_methods') ? 'class="active"' : ''; ?>>
                                    <?php echo link_to('/client/payment_methods', trans('texts.payment_methods') ); ?>

                                </li>
                            <?php endif; ?>
                        <?php endif; ?>
                    </ul>
                </div><!--/.nav-collapse -->
            </div>
        </nav>
        <div class="container">
            <?php echo $__env->make('partials.warn_session', ['redirectTo' => '/'], array_except(get_defined_vars(), array('__data', '__path')))->render(); ?>
            <?php if(Session::has('warning')): ?>
                <div class="alert alert-warning"><?php echo Session::get('warning'); ?></div>
            <?php endif; ?>
            <?php if(Session::has('message')): ?>
                <div class="alert alert-info"><?php echo Session::get('message'); ?></div>
            <?php endif; ?>

            <?php if(Session::has('error')): ?>
                <div class="alert alert-danger"><?php echo Session::get('error'); ?></div>
            <?php endif; ?>
        </div>
    </div>
    <div id="mainContent" class="container">
        <?php echo $__env->yieldContent('content'); ?>
    </div>
    <footer id="footer" role="contentinfo">
        <div class="bottom">
            <div class="wrap">
                <div class="copy">Copyright &copy;<?php echo e(date('Y')); ?>

                    <a href="#" target="_blank"> <strong><?php echo e(trans('texts.team_source')); ?></strong> </a>. All rights
                    reserved.
                </div>
            </div><!-- .wrap -->
        </div><!-- .bottom -->
    </footer><!-- #footer -->
<?php $__env->stopSection(); ?>

<?php echo $__env->make('master', array_except(get_defined_vars(), array('__data', '__path')))->render(); ?>