<?php $__env->startSection('head'); ?>
    ##parent-placeholder-1a954628a960aaef81d7b2d4521929579f3541e6##
    <?php echo $__env->make('money_script', array_except(get_defined_vars(), array('__data', '__path')))->render(); ?>
    <link href="<?php echo e(asset('css/jsoneditor.min.css')); ?>" rel="stylesheet" type="text/css">
    <script src="<?php echo e(asset('js/jsoneditor.min.js')); ?>" type="text/javascript"></script>

    <?php $__currentLoopData = $account->getFontFolders(); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $font): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
        <script src="<?php echo e(asset('js/vfs_fonts/'.$font.'.js')); ?>" type="text/javascript"></script>
    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
    <script src="<?php echo e(asset('pdf.built.js')); ?>?no_cache=<?php echo e(NINJA_VERSION); ?>" type="text/javascript"></script>

    <style type="text/css">

        select.form-control {
            background: #FFFFFF !important;
            margin-right: 12px;
        }

        table {
            background: #FFFFFF !important;
        }

        /* http://stackoverflow.com/questions/4810841/how-can-i-pretty-print-json-using-javascript */
        pre {
            outline: 1px solid #ccc;
            padding: 5px;
            margin: 5px;
        }

        .string {
            color: green;
        }

        .number {
            color: red;
        }

        .boolean {
            color: blue;
        }

        .null {
            color: gray;
        }

        .key {
            color: black;
        }

    </style>

<?php $__env->stopSection(); ?>

<?php $__env->startSection('content'); ?>
    ##parent-placeholder-040f06fd774092478d450774f5ba30c5da78acc8##

    <script>
        var invoiceDesigns = <?php echo $invoiceDesigns; ?>;
        var invoiceFonts = <?php echo $invoiceFonts; ?>;
        var invoice = <?php echo json_encode($invoice); ?>;
        var sections = ['content', 'styles', 'defaultStyle', 'pageMargins', 'header', 'footer', 'background'];
        var customDesign = origCustomDesign = <?php echo $customDesign ?: 'JSON.parse(invoiceDesigns[0].javascript);'; ?>;

        function getPDFString(cb, force) {
            invoice.invoice_design_id = $('#invoice_design_id').val();
            invoice.features = {
                customize_invoice_design:<?php echo e(Auth::user()->hasFeature(FEATURE_CUSTOMIZE_INVOICE_DESIGN) ? 'true' : 'false'); ?>,
                remove_created_by:<?php echo e(Auth::user()->hasFeature(FEATURE_REMOVE_CREATED_BY) ? 'true' : 'false'); ?>,
                invoice_settings:<?php echo e(Auth::user()->hasFeature(FEATURE_INVOICE_SETTINGS) ? 'true' : 'false'); ?>

            };
            invoice.account.hide_paid_to_date = <?php echo Auth::user()->account->hide_paid_to_date ? 'true' : 'false'; ?>;
            NINJA.primaryColor = <?php echo json_encode(Auth::user()->account->primary_color); ?>;
            NINJA.secondaryColor = <?php echo json_encode(Auth::user()->account->secondary_color); ?>;
            NINJA.fontSize = <?php echo Auth::user()->account->font_size; ?>;
            NINJA.headerFont = <?php echo json_encode(Auth::user()->account->getHeaderFontName()); ?>;
            NINJA.bodyFont = <?php echo json_encode(Auth::user()->account->getBodyFontName()); ?>;

            generatePDF(invoice, getDesignJavascript(), force, cb);
        }

        function getDesignJavascript() {
            var id = $('#invoice_design_id').val();
            if (id == '-1') {
                showMoreDesigns();
                $('#invoice_design_id').val(1);
                return invoiceDesigns[0].javascript;
            } else if (customDesign) {
                return JSON.stringify(customDesign);
            } else {
                return invoiceDesigns[0].javascript;
            }
        }

        function loadEditor(section) {
            if (section == 'defaults') {
                section = 'defaultStyle';
            } else if (section == 'margins') {
                section = 'pageMargins';
            }

            editorSection = section;
            editor.set(customDesign[section]);

            // the function throws an error if the editor is in code view
            try {
                editor.expandAll();
            } catch (err) {
            }
        }

        function saveEditor(data) {
            setTimeout(function () {
                customDesign[editorSection] = editor.get();
                clearError();
                refreshPDF();
            }, 100)
        }

        function onSelectChange() {
            var $select = $('#invoice_design_id');
            var id = $select.val();
            $select.val(null).blur();

            if (parseInt(id)) {
                var design = _.find(invoiceDesigns, function (design) {
                    return design.id == id
                });
                customDesign = JSON.parse(design.javascript);
            } else {
                customDesign = origCustomDesign;
            }

            loadEditor(editorSection);
            clearError();
            refreshPDF(true);
        }

        function submitForm() {
            if (!NINJA.isPDFValid) {
                return;
            }

            $('#custom_design').val(JSON.stringify(customDesign));
            $('form.warn-on-exit').submit();
        }

        window.onerror = function (e) {
            $('#pdf-error').html(e.message ? e.message : e).show();
            $('button.save-button').prop('disabled', true);
            NINJA.isPDFValid = false;
        }

        function clearError() {
            NINJA.isPDFValid = true;
            $('#pdf-error').hide();
            $('button.save-button').prop('disabled', false);
        }

        $(function () {
            clearError();

            var container = document.getElementById("jsoneditor");
            var options = {
                mode: 'form',
                modes: ['form', 'code'],
                change: function () {
                    saveEditor();
                    NINJA.formIsChanged = true;
                }
            };
            window.editor = new JSONEditor(container, options);
            loadEditor('content');

            $('a[data-toggle="tab"]').on('shown.bs.tab', function (e) {
                var target = $(e.target).attr("href") // activated tab
                target = target.substring(1); // strip leading #
                loadEditor(target);
            });

            refreshPDF(true);
        });

    </script>

    <div class="row">
        <div class="col-md-6">

            <?php echo Former::open()->addClass('warn-on-exit'); ?>


            <div style="display:none">
                <?php echo Former::text('custom_design'); ?>

            </div>

            <div role="tabpanel">
                <ul class="nav nav-tabs" role="tablist" style="border: none">
                    <li role="presentation" class="active"><a href="#content" aria-controls="content" role="tab"
                                                              data-toggle="tab"><?php echo e(trans('texts.content')); ?></a></li>
                    <li role="presentation"><a href="#styles" aria-controls="styles" role="tab"
                                               data-toggle="tab"><?php echo e(trans('texts.styles')); ?></a></li>
                    <li role="presentation"><a href="#defaults" aria-controls="defaults" role="tab"
                                               data-toggle="tab"><?php echo e(trans('texts.defaults')); ?></a></li>
                    <li role="presentation"><a href="#margins" aria-controls="margins" role="tab"
                                               data-toggle="tab"><?php echo e(trans('texts.margins')); ?></a></li>
                    <li role="presentation"><a href="#header" aria-controls="header" role="tab"
                                               data-toggle="tab"><?php echo e(trans('texts.header')); ?></a></li>
                    <li role="presentation"><a href="#footer" aria-controls="footer" role="tab"
                                               data-toggle="tab"><?php echo e(trans('texts.footer')); ?></a></li>
                    <?php if($account->isEnterprise() && $account->background_image_id): ?>
                        <li role="presentation"><a href="#background" aria-controls="footer" role="tab"
                                                   data-toggle="tab"><?php echo e(trans('texts.background')); ?></a></li>
                    <?php endif; ?>
                </ul>
            </div>
            <div id="jsoneditor" style="width: 100%; height: 814px;"></div>
            <p>&nbsp;</p>

            <div>
                <?php echo Former::select('invoice_design_id')
                        ->placeholder(trans('texts.load_design'))
                        ->style('display:inline;width:180px')
                        ->fromQuery($invoiceDesigns, 'name', 'id')
                        ->onchange('onSelectChange()')
                        ->raw(); ?>

                <div class="pull-right">
                    <?php echo Button::normal(trans('texts.help'))->withAttributes(['onclick' => 'showHelp()'])->appendIcon(Icon::create('question-sign')); ?>

                    <?php echo Button::normal(trans('texts.cancel'))->asLinkTo(URL::to('/settings/invoice_design'))->appendIcon(Icon::create('remove-circle')); ?>

                    <?php if(Auth::user()->hasFeature(FEATURE_CUSTOMIZE_INVOICE_DESIGN)): ?>
                        <?php echo Button::success(trans('texts.save'))->withAttributes(['onclick' => 'submitForm()'])->appendIcon(Icon::create('floppy-disk'))->withAttributes(['class' => 'save-button']); ?>

                    <?php endif; ?>
                </div>
            </div>

            <script>

                function showHelp() {
                    $('#designHelpModal').modal('show');
                }

            </script>

            <?php echo Former::close(); ?>


            <div class="modal fade" id="designHelpModal" tabindex="-1" role="dialog"
                 aria-labelledby="designHelpModalLabel" aria-hidden="true">
                <div class="modal-dialog">
                    <div class="modal-content">
                        <div class="modal-header">
                            <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
                            <h4 class="modal-title" id="designHelpModalLabel"><?php echo e(trans('texts.help')); ?></h4>
                        </div>

                        <div class="container" style="width: 100%; padding-bottom: 0px !important">
                            <div class="panel panel-default">
                                <div class="panel-body">
                                    <?php echo trans('texts.customize_help', [
                                            'pdfmake_link' => link_to('http://pdfmake.org', 'pdfmake', ['target' => '_blank']),
                                            'playground_link' => link_to('http://pdfmake.org/playground.html', trans('texts.playground'), ['target' => '_blank']),
                                            'forum_link' => link_to('https://www.eninjaplus.com/forums/forum/support', trans('texts.support_forum'), ['target' => '_blank']),
                                        ]); ?><br/>

                                    <?php echo $__env->make('partials/variables_help', ['entityType' => ENTITY_INVOICE, 'account' => $account], array_except(get_defined_vars(), array('__data', '__path')))->render(); ?>
                                </div>
                            </div>
                        </div>

                        <div class="modal-footer">
                            <button type="button" class="btn btn-default"
                                    data-dismiss="modal"><?php echo e(trans('texts.close')); ?></button>
                            <a class="btn btn-primary" href="<?php echo e(config('ninja.video_urls.custom_design')); ?>"
                               target="_blank"><?php echo e(trans('texts.video')); ?></a>
                        </div>

                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-6">
            <div id="pdf-error" class="alert alert-danger" style="display:none"></div>

            <?php echo $__env->make('invoices.pdf', ['account' => Auth::user()->account, 'pdfHeight' => 930], array_except(get_defined_vars(), array('__data', '__path')))->render(); ?>

        </div>
    </div>

<?php $__env->stopSection(); ?>

<?php echo $__env->make('header', array_except(get_defined_vars(), array('__data', '__path')))->render(); ?>