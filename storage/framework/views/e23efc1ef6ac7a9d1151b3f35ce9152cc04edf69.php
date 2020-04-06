<!DOCTYPE html>
<html lang="<?php echo e(App::getLocale()); ?>">
<head>
    <title>Invoice Ninja | Setup</title>
    <meta charset="utf-8">
    <meta name="csrf-token" content="<?= csrf_token() ?>">
    <script src="<?php echo e(asset('built.js')); ?>?no_cache=<?php echo e(NINJA_VERSION); ?>" type="text/javascript"></script>
    <link href="<?php echo e(asset('css/built.public.css')); ?>?no_cache=<?php echo e(NINJA_VERSION); ?>" rel="stylesheet" type="text/css"/>
    <link href="<?php echo e(asset('css/built.css')); ?>?no_cache=<?php echo e(NINJA_VERSION); ?>" rel="stylesheet" type="text/css"/>
    <link href="<?php echo e(asset('favicon.png?test')); ?>" rel="shortcut icon">

    <style type="text/css">
        body {
            background-color: #FEFEFE;
        }
    </style>

</head>

<body>
<div class="container">

    &nbsp;
    <div class="row">
        <div class="col-md-8 col-md-offset-2">

            <div class="jumbotron">
                <h2>Invoice Ninja Setup</h2>
                <?php if(version_compare(phpversion(), '7.0.0', '<')): ?>
                    <div class="alert alert-warning">Warning: The application requires PHP >= 7.0.0</div>
                <?php endif; ?>
                <?php if(!function_exists('proc_open')): ?>
                    <div class="alert alert-warning">Warning: <a href="http://php.net/manual/en/function.proc-open.php"
                                                                 target="_blank">proc_open</a> must be enabled.
                    </div>
                <?php endif; ?>
                <?php if(!@fopen(base_path()."/.env", 'a')): ?>
                    <div class="alert alert-warning">Warning: Permission denied to write .env config file
                        <pre>sudo chown www-data:www-data <?php echo e(base_path('.env')); ?></pre>
                    </div>
                <?php endif; ?>
                If you need help you can either post to our <a href="https://www.invoiceninja.com/forums/forum/support/"
                                                               target="_blank">support forum</a> or email us at <a
                        href="mailto:contact@invoiceninja.com" target="_blank">contact@invoiceninja.com</a>.

                <?php if(! env('PRECONFIGURED_INSTALL')): ?>
                    <p>
                    <pre>-- Commands to create a MySQL database and user
CREATE SCHEMA `ninja` DEFAULT CHARACTER SET utf8 COLLATE utf8_general_ci;
CREATE USER 'ninja'@'localhost' IDENTIFIED BY 'ninja';
GRANT ALL PRIVILEGES ON `ninja`.* TO 'ninja'@'localhost';
FLUSH PRIVILEGES;</pre>
                    </p>
                <?php endif; ?>
            </div>

            <?php echo Former::open()->rules([
                'app[url]' => 'required',
                'database[type][host]' => 'required',
                'database[type][database]' => 'required',
                'database[type][username]' => 'required',
                'first_name' => 'required',
                'last_name' => 'required',
                'email' => 'required|email',
                'password' => 'required',
                'terms_checkbox' => 'required',
                'privacy_checkbox' => 'required'
              ]); ?>


            <div style="display:<?php echo e(env('PRECONFIGURED_INSTALL') ? 'none' : 'block'); ?>">
                <?php echo $__env->make('partials.system_settings', array_except(get_defined_vars(), array('__data', '__path')))->render(); ?>
            </div>

            <div class="panel panel-default">
                <div class="panel-heading">
                    <h3 class="panel-title">User Details</h3>
                </div>
                <div class="panel-body">
                    <?php echo Former::text('first_name'); ?>

                    <?php echo Former::text('last_name'); ?>

                    <?php echo Former::text('email'); ?>

                    <?php echo Former::password('password'); ?>

                </div>
            </div>

            <?php echo Former::checkbox('terms_checkbox')->label(' ')->text(trans('texts.agree_to_terms', ['terms' => '<a href="'.config('ninja.terms_of_service_url.selfhost').'" target="_blank">'.trans('texts.terms_of_service').'</a>']))->value(1); ?>

            <?php echo Former::checkbox('privacy_checkbox')->label(' ')->text(trans('texts.agree_to_terms', ['terms' => '<a href="'.config('ninja.privacy_policy_url.selfhost').'" target="_blank">'.trans('texts.privacy_policy').'</a>']))->value(1); ?>

            <?php echo Former::actions( Button::primary('Submit')->large()->submit() ); ?>

            <?php echo Former::close(); ?>


        </div>
</body>
</html>
