<?php $__env->startSection('content'); ?>
    ##parent-placeholder-040f06fd774092478d450774f5ba30c5da78acc8##

    <?php echo $__env->make('accounts.nav', ['selected' => ACCOUNT_PAYMENT_TERMS], array_except(get_defined_vars(), array('__data', '__path')))->render(); ?>

    <?php echo Button::primary(trans('texts.create_payment_term'))
          ->asLinkTo(URL::to('/payment_terms/create'))
          ->withAttributes(['class' => 'pull-right'])
          ->appendIcon(Icon::create('plus-sign')); ?>


    <?php echo $__env->make('partials.bulk_form', ['entityType' => ENTITY_PAYMENT_TERM], array_except(get_defined_vars(), array('__data', '__path')))->render(); ?>

    <?php echo Datatable::table()
        ->addColumn(
          trans('texts.num_days'),
          trans('texts.created_at'),
          trans('texts.updated_at'),
          trans('texts.created_by'),
          trans('texts.updated_by'),
          trans('texts.action'))
        ->setUrl(url('api/payment_terms/'))
        ->setOptions('sPaginationType', 'bootstrap')
        ->setOptions('bFilter', false)
        ->setOptions('bAutoWidth', false)
        ->setOptions('aoColumns', [[ "sWidth"=> "50%" ], [ "sWidth"=> "50%" ]])
        ->setOptions('aoColumnDefs', [['bSortable'=>false, 'aTargets'=>[1]]])
        ->render('datatable'); ?>


    <script>
        window.onDatatableReady = actionListHandler;
    </script>


<?php $__env->stopSection(); ?>

<?php echo $__env->make('header', array_except(get_defined_vars(), array('__data', '__path')))->render(); ?>