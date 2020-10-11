<?php $__env->startSection('content'); ?>
    ##parent-placeholder-040f06fd774092478d450774f5ba30c5da78acc8##

    <?php echo $__env->make('accounts.nav', ['selected' => ACCOUNT_API_TOKENS, 'advanced' => true], array_except(get_defined_vars(), array('__data', '__path')))->render(); ?>

    <div class="row">
        <div class="col-md-12">
            
            
            
            
            <?php if(Utils::hasFeature(FEATURE_API)): ?>
                <?php echo Button::primary(trans('texts.add_token'))->asLinkTo(URL::to('/tokens/create'))->appendIcon(Icon::create('plus-sign')); ?>

            <?php endif; ?>
        </div>
    </div>
    <br>
    <div class="row">
        <?php if(Utils::hasFeature(FEATURE_API)): ?>
            <div class="col-md-12">
                <?php echo $__env->make('list',[
                 'entityType' => ENTITY_TOKEN,
                'datatable' => new \App\Ninja\Datatables\TokenDatatable(true, true),
                'url' => url('api/tokens/'),
                ], array_except(get_defined_vars(), array('__data', '__path')))->render(); ?>
            </div>
        <?php endif; ?>
    </div>

    <script>
        window.onDatatableReady = actionListHandler;
    </script>
    <p>&nbsp;</p>

    
    
    
    

<?php $__env->stopSection(); ?>

<?php echo $__env->make('header', array_except(get_defined_vars(), array('__data', '__path')))->render(); ?>