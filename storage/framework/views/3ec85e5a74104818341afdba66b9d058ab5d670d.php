<?php $__env->startSection('form'); ?>
    <?php echo $__env->make('partials.warn_session', ['redirectTo' => '/client/session_expired'], array_except(get_defined_vars(), array('__data', '__path')))->render(); ?>
    <div class="container">
        <?php echo Former::open()
                ->rules(['email' => 'required|email'])
                ->addClass('form-signin'); ?>


        <h2 class="form-signin-heading"><?php echo e(trans('texts.password_recovery')); ?></h2>
        <hr class="green">

        <?php if(count($errors->all())): ?>
            <div class="alert alert-danger">
                <?php $__currentLoopData = $errors->all(); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $error): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                    <li><?php echo e($error); ?></li>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
            </div>
        <?php endif; ?>

        <?php if(session('status')): ?>
            <div class="alert alert-info">
                <?php echo e(session('status')); ?>

            </div>
        <?php endif; ?>

        <!-- if there are login errors, show them here -->
        <?php if(Session::has('warning')): ?>
            <div class="alert alert-warning"><?php echo e(Session::get('warning')); ?></div>
        <?php endif; ?>

        <?php if(Session::has('message')): ?>
            <div class="alert alert-info"><?php echo e(Session::get('message')); ?></div>
        <?php endif; ?>

        <?php if(Session::has('error')): ?>
            <div class="alert alert-danger"><?php echo e(Session::get('error')); ?></div>
        <?php endif; ?>

        <div>
            <?php echo Former::text('email')->placeholder(trans('texts.email_address'))->raw(); ?>

        </div>
        <?php echo Button::success(trans('texts.send_email'))
                    ->withAttributes(['class' => 'green'])
                    ->large()->submit()->block(); ?>


        <div class="row meta">
            <div class="col-md-12 col-sm-12" style="text-align:center;padding-top:8px;">
                <?php echo link_to('/client/login' . (request()->account_key ? '?account_key=' . request()->account_key : ''), trans('texts.return_to_login')); ?>

            </div>
        </div>

        <?php echo Former::close(); ?>

    </div>

    <script type="text/javascript">
        $(function() {
            $('.form-signin').submit(function() {
                $('button.btn-success').prop('disabled', true);
            });
        })
    </script>

<?php $__env->stopSection(); ?>

<?php echo $__env->make('client_login', array_except(get_defined_vars(), array('__data', '__path')))->render(); ?>