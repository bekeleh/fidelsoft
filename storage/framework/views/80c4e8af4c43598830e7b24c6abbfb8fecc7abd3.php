<?php $__env->startSection('head'); ?>
    ##parent-placeholder-1a954628a960aaef81d7b2d4521929579f3541e6##

    <?php echo $__env->make('money_script', array_except(get_defined_vars(), array('__data', '__path')))->render(); ?>
    <?php $__currentLoopData = Auth::user()->account->getFontFolders(); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $font): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
        <script src="<?php echo e(asset('js/vfs_fonts/'.$font.'.js')); ?>" type="text/javascript"></script>
    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
    <script src="<?php echo e(asset('pdf.built.js')); ?>?no_cache=<?php echo e(NINJA_VERSION); ?>" type="text/javascript"></script>

    <script>

        var invoiceDesigns = <?php echo $invoiceDesigns; ?>;
        var invoiceFonts = <?php echo $invoiceFonts; ?>;
        var currentInvoice = <?php echo $invoice; ?>;
        var versionsJson = <?php echo strip_tags($versionsJson); ?>;

        function getPDFString(cb) {

            var version = $('#version').val();
            var invoice;

            <?php if($paymentId): ?>
                invoice = versionsJson[0];
            <?php else: ?>
            if (parseInt(version)) {
                invoice = versionsJson[version];
            } else {
                invoice = currentInvoice;
            }
            <?php endif; ?>

                invoice.image = window.accountLogo;

            var invoiceDesignId = parseInt(invoice.invoice_design_id);
            var invoiceDesign = _.findWhere(invoiceDesigns, {id: invoiceDesignId});
            if (!invoiceDesign) {
                invoiceDesign = invoiceDesigns[0];
            }

            generatePDF(invoice, invoiceDesign.javascript, true, cb);
        }

        $(function () {
            refreshPDF();
        });

    </script>

<?php $__env->stopSection(); ?>

<?php $__env->startSection('content'); ?>

    <?php echo Former::open()->addClass('form-inline')->onchange('refreshPDF()'); ?>


    <?php if(count($versionsSelect) > 1): ?>
        <?php echo Former::select('version')
                ->options($versionsSelect)
                ->label(trans('select_version'))
                ->style('background-color: white !important'); ?>

    <?php endif; ?>

    <?php echo Button::primary(trans('texts.edit_' . $invoice->getEntityType()))
            ->asLinkTo(URL::to('/' . $invoice->getEntityType() . 's/' . $invoice->public_id . '/edit'))
            ->appendIcon(Icon::create('edit'))
            ->withAttributes(array('class' => 'pull-right')); ?>

    <?php echo Former::close(); ?>


    <br/>&nbsp;<br/>

    <?php if(count($versionsSelect) <= 1): ?>
        <br/>&nbsp;<br/>
    <?php endif; ?>

    <?php echo $__env->make('invoices.pdf', ['account' => Auth::user()->account, 'pdfHeight' => 800], array_except(get_defined_vars(), array('__data', '__path')))->render(); ?>

    <?php if(Utils::hasFeature(FEATURE_DOCUMENTS) && $invoice->account->invoice_embed_documents): ?>
        <?php $__currentLoopData = $invoice->documents; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $document): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
            <?php if($document->isPDFEmbeddable()): ?>
                <script src="<?php echo e($document->getVFSJSUrl()); ?>" type="text/javascript" async></script>
            <?php endif; ?>
        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
        <?php $__currentLoopData = $invoice->expenses; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $expense): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
            <?php $__currentLoopData = $expense->documents; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $document): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                <?php if($document->isPDFEmbeddable()): ?>
                    <script src="<?php echo e($document->getVFSJSUrl()); ?>" type="text/javascript" async></script>
                <?php endif; ?>
            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
    <?php endif; ?>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('header', array_except(get_defined_vars(), array('__data', '__path')))->render(); ?>