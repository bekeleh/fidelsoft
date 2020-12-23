<?php use App\Ninja\Datatables\SubscriptionDatatable;

$__env->startSection('content'); ?>
    ##parent-placeholder-040f06fd774092478d450774f5ba30c5da78acc8##

    <?php echo $__env->make('accounts.nav', ['selected' => ACCOUNT_API_TOKENS, 'advanced' => true], array_except(get_defined_vars(), array('__data', '__path')))->render(); ?>
    <h3 style="height:20px;"></h3>
    <br/>
    <div class="row">
        <div class="col-md-12">
            <?php echo $__env->make('list',
            [
            'entityType' => ENTITY_SUBSCRIPTION,
            'datatable' => new SubscriptionDatatable(true, true),
            'url' => url('api/subscriptions/'),
            ], array_except(get_defined_vars(), array('__data', '__path')))->render(); ?>

        </div>
    </div>
    <script>
        window.onDatatableReady = actionListHandler;
    </script>
    <p>&nbsp;</p>

    
    
    
    

<?php $__env->stopSection(); ?>

<?php echo $__env->make('header', array_except(get_defined_vars(), array('__data', '__path')))->render(); ?>