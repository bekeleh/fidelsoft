<?php $__env->startSection('content'); ?>
    ##parent-placeholder-040f06fd774092478d450774f5ba30c5da78acc8##
    <?php echo $__env->make('accounts.nav', ['selected' => ACCOUNT_BANKS], array_except(get_defined_vars(), array('__data', '__path')))->render(); ?>

    <?php if(isset($warnPaymentGateway) && $warnPaymentGateway): ?>
        <div class="alert alert-warning"><?php echo trans('texts.warn_payment_gateway', ['link' => link_to('/gateways/create', trans('texts.click_here'))]); ?></div>
    <?php endif; ?>

    <?php if(Auth::user()->hasFeature(FEATURE_EXPENSES)): ?>
        <div class="pull-right">
            <?php echo Button::normal(trans('texts.import_ofx'))
                ->asLinkTo(URL::to('/bank_accounts/import_ofx'))
                ->appendIcon(Icon::create('open')); ?>

            <?php echo Button::primary(trans('texts.add_bank_account'))
                ->asLinkTo(URL::to('/bank_accounts/create'))
                ->appendIcon(Icon::create('plus-sign')); ?>

        </div>
    <?php endif; ?>

    <?php echo $__env->make('partials.bulk_form', ['entityType' => ENTITY_BANK_ACCOUNT], array_except(get_defined_vars(), array('__data', '__path')))->render(); ?>

    <?php echo Datatable::table()
        ->addColumn(
            trans('texts.bank_name'),
            trans('texts.integration_type'),
            trans('texts.action'))
        ->setUrl(url('api/bank_accounts/'))
        ->setOptions('sPaginationType', 'bootstrap')
        ->setOptions('bFilter', true)
        ->setOptions('bAutoWidth', true)
        ->setOptions('aoColumnDefs', [['bSortable'=>true, 'aTargets'=>[1]]])
        ->render('datatable'); ?>


    <script>
        window.onDatatableReady = actionListHandler;
    </script>

<?php $__env->stopSection(); ?>

<?php echo $__env->make('header', array_except(get_defined_vars(), array('__data', '__path')))->render(); ?>