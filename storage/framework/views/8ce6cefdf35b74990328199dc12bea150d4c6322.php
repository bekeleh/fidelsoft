<?php $__env->startSection('content'); ?>
    ##parent-placeholder-040f06fd774092478d450774f5ba30c5da78acc8##
    <?php echo $__env->make('accounts.nav', ['selected' => ACCOUNT_USER_MANAGEMENT, 'advanced' => true], array_except(get_defined_vars(), array('__data', '__path')))->render(); ?>
    <?php if(Utils::hasFeature(FEATURE_USERS)): ?>
        <?php if(Auth::user()->canAddUsers() || Auth::user()->isSuperUser): ?>
            <?php echo $__env->make('list',
            [
            'entityType' => ENTITY_USER,
            'datatable' => new \App\Ninja\Datatables\UserDatatable(true, true),
            'url' => url('api/users/'),
            ], array_except(get_defined_vars(), array('__data', '__path')))->render(); ?>
        <?php endif; ?>
    <?php elseif(Utils::isTrial()): ?>
        <div class="alert alert-warning"><?php echo trans('texts.add_users_not_supported'); ?></div>
    <?php endif; ?>

    <script>
        window.onDatatableReady = actionListHandler;

        function setTrashVisible() {
            var checked = $('#trashed').is(':checked');
            var url = '<?php echo e(URL::to('set_entity_filter/user')); ?>' + (checked ? '/active,archived' : '/active');
            $.get(url, function (data) {
                refreshDatatable();
            })
        }
    </script>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('header', array_except(get_defined_vars(), array('__data', '__path')))->render(); ?>