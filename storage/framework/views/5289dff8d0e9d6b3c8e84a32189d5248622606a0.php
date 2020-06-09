<?php $__env->startSection('form'); ?>

<?php echo $__env->make('partials.warn_session', ['redirectTo' => '/client/session_expired'], array_except(get_defined_vars(), array('__data', '__path')))->render(); ?>

    <div class="container">

        <?php echo Former::open()
            ->rules(['password' => 'required'])
            ->addClass('form-signin'); ?>


        <h2 class="form-signin-heading"><?php echo e(trans('texts.client_login')); ?></h2>
        <hr class="green">


        <?php if (count($errors->all())): ?>
            <div class="alert alert-danger">
                <?php $__currentLoopData = $errors->all();
                $__env->addLoop($__currentLoopData);
                foreach ($__currentLoopData as $error): $__env->incrementLoopIndices();
                    $loop = $__env->getLastLoop(); ?>
                    <li><?php echo e($error); ?></li>
                <?php endforeach;
                $__env->popLoop();
                $loop = $__env->getLastLoop(); ?>
            </div>
        <?php endif; ?>

        <?php if (Session::has('warning')): ?>
            <div class="alert alert-warning"><?php echo e(Session::get('warning')); ?></div>
        <?php endif; ?>

        <?php if (Session::has('message')): ?>
            <div class="alert alert-info"><?php echo e(Session::get('message')); ?></div>
        <?php endif; ?>

        <?php if (Session::has('error')): ?>
            <div class="alert alert-danger">
                <li><?php echo e(Session::get('error')); ?></li>
            </div>
        <?php endif; ?>
        <?php echo e(Former::populateField('remember', 'true')); ?>

        <div>

            <?php echo Former::text('email')->placeholder(trans('texts.email'))->raw(); ?>

            <?php echo Former::password('password')->placeholder(trans('texts.password'))->raw(); ?>

        </div>
        <?php echo Former::hidden('remember')->raw(); ?>


        <?php echo Button::success(trans('texts.login'))
            ->withAttributes(['id' => 'loginButton', 'class' => 'green'])
            ->large()->submit()->block(); ?>

        <div class="row meta">
            <div class="col-md-12 col-sm-12" style="text-align:center;padding-top:8px;">
                <?php echo link_to('/client/recover_password' . (request()->account_key ? '?account_key=' . request()->account_key : ''), trans('texts.recover_password')); ?>

            </div>
        </div>
        <?php echo Former::close(); ?>

    </div>

    <script type="text/javascript">
        $(function () {
            if ($('#email').val()) {
                $('#password').focus();
            } else {
                $('#email').focus();
            }
        })
    </script>

<?php $__env->stopSection(); ?>

<?php echo $__env->make('client_login', array_except(get_defined_vars(), array('__data', '__path')))->render(); ?>