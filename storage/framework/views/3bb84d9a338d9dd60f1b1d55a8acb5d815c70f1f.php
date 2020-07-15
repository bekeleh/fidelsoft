

<?php use App\Ninja\Datatables\SubscriptionDatatable;
use App\Ninja\Datatables\TokenDatatable;

$__env->startSection('content'); ?>
    ##parent-placeholder-040f06fd774092478d450774f5ba30c5da78acc8##

    <?php echo $__env->make('accounts.nav', ['selected' => ACCOUNT_API_TOKENS, 'advanced' => true], array_except(get_defined_vars(), array('__data', '__path')))->render(); ?>
    <div class="row">
        <div class="col-md-12">
            <?php echo Button::normal(trans('texts.documentation'))->asLinkTo(NINJA_WEB_URL.'/api-documentation/')->withAttributes(['target' => '_blank'])->appendIcon(Icon::create('info-sign')); ?>

            <?php if(!Utils::isReseller()): ?>
                <?php echo Button::normal(trans('texts.zapier'))->asLinkTo(ZAPIER_URL)->withAttributes(['target' => '_blank'])->appendIcon(Icon::create('globe')); ?>

            <?php endif; ?>
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
                'datatable' => new TokenDatatable(true, true),
                'url' => url('api/tokens/'),
                ], array_except(get_defined_vars(), array('__data', '__path')))->render(); ?>
            </div>
        <?php endif; ?>
    </div>
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

    <?php if(!Utils::isReseller()): ?>
        <p>&nbsp;</p>
        <script src="https://zapier.com/zapbook/embed/widget.js?guided_zaps=5627,6025,12216,8805,5628,6027&container=false&limit=6"></script>
    <?php endif; ?>

<?php $__env->stopSection(); ?>

<?php echo $__env->make('header', array_except(get_defined_vars(), array('__data', '__path')))->render(); ?>