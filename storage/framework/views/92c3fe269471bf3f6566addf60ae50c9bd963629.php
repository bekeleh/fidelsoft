<?php $__env->startSection('head'); ?>
    ##parent-placeholder-1a954628a960aaef81d7b2d4521929579f3541e6##

    <?php echo $__env->make('money_script', array_except(get_defined_vars(), array('__data', '__path')))->render(); ?>
    <?php $__currentLoopData = $account->getFontFolders(); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $font): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
        <script src="<?php echo e(asset('js/vfs_fonts/'.$font.'.js')); ?>" type="text/javascript"></script>
    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
    <script src="<?php echo e(asset('pdf.built.js')); ?>?no_cache=<?php echo e(NINJA_VERSION); ?>" type="text/javascript"></script>
    <script src="<?php echo e(asset('js/lightbox.min.js')); ?>" type="text/javascript"></script>
    <link href="<?php echo e(asset('css/lightbox.css')); ?>" rel="stylesheet" type="text/css"/>
<?php $__env->stopSection(); ?>

<?php $__env->startSection('head_css'); ?>
    ##parent-placeholder-65e7fa855b4f81a209a50c6e440870f25d0240e1##

    <style type="text/css">
        .label-group {
            display: none;
        }
    </style>
<?php $__env->stopSection(); ?>

<?php $__env->startSection('content'); ?>
    ##parent-placeholder-040f06fd774092478d450774f5ba30c5da78acc8##
    <?php echo $__env->make('accounts.nav', ['selected' => ACCOUNT_INVOICE_DESIGN, 'advanced' => true], array_except(get_defined_vars(), array('__data', '__path')))->render(); ?>
    <?php echo $__env->make('accounts.partials.invoice_fields', array_except(get_defined_vars(), array('__data', '__path')))->render(); ?>

    <script>
        var invoiceDesigns = <?php echo $invoiceDesigns; ?>;
        var invoiceFonts = <?php echo $invoiceFonts; ?>;
        var invoice = <?php echo json_encode($invoice); ?>;

        function getDesignJavascript() {
            var id = $('#invoice_design_id').val();
            if (id == '-1') {
                showMoreDesigns();
                $('#invoice_design_id').val(1);
                return invoiceDesigns[0].javascript;
            } else {
                var design = _.find(invoiceDesigns, function (design) {
                    return design.id == id
                });
                return design ? design.javascript : '';
            }
        }

        function loadFont(fontId) {
            var fontFolder = '';
            $.each(window.invoiceFonts, function (i, font) {
                if (font.id == fontId) fontFolder = font.folder;
            });
            if (!window.ninjaFontVfs[fontFolder]) {
                window.loadingFonts = true;
                jQuery.getScript(<?php echo json_encode(asset('js/vfs_fonts/%s.js')); ?>.replace('%s', fontFolder), function () {
                    window.loadingFonts = false;
                    ninjaLoadFontVfs();
                    refreshPDF()
                }
            )
            }
        }

        function getPDFString(cb) {
            invoice.features = {
                customize_invoice_design:<?php echo e(Auth::user()->hasFeature(FEATURE_CUSTOMIZE_INVOICE_DESIGN) ? 'true' : 'false'); ?>,
                remove_created_by:<?php echo e(Auth::user()->hasFeature(FEATURE_REMOVE_CREATED_BY) ? 'true' : 'false'); ?>,
                invoice_settings:<?php echo e(Auth::user()->hasFeature(FEATURE_INVOICE_SETTINGS) ? 'true' : 'false'); ?>

            };
            invoice.account.invoice_embed_documents = $('#invoice_embed_documents').is(":checked");
            invoice.account.hide_paid_to_date = $('#hide_paid_to_date').is(":checked");
            invoice.invoice_design_id = $('#invoice_design_id').val();
            invoice.account.page_size = $('#page_size option:selected').text();
            invoice.account.invoice_fields = ko.mapping.toJSON(model);

            NINJA.primaryColor = $('#primary_color').val();
            NINJA.secondaryColor = $('#secondary_color').val();
            NINJA.fontSize = parseInt($('#font_size').val());
            NINJA.headerFont = $('#header_font_id option:selected').text();
            NINJA.bodyFont = $('#body_font_id option:selected').text();

            var fields = <?php echo json_encode(App\Models\Account::$customLabels); ?>;
            for (var i = 0; i < fields.length; i++) {
                var field = fields[i];
                var val = $('#labels_' + field).val();
                if (!invoiceLabels[field + '_orig']) {
                    invoiceLabels[field + '_orig'] = invoiceLabels[field];
                }
                invoiceLabels[field] = val || invoiceLabels[field + '_orig'];
            }

            generatePDF(invoice, getDesignJavascript(), true, cb);
        }

        function updateFieldLabels() {
            <?php $__currentLoopData = App\Models\Account::$customLabels; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $field): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
            if ($('#labels_<?php echo e($field); ?>').val()) {
                $('.<?php echo e($field); ?>-label-group').show();
            } else {
                $('.<?php echo e($field); ?>-label-group').hide();
            }
            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
        }

        function onFieldChange() {
            var $select = $('#label_field');
            var id = $select.val();
            $select.val(null).blur();
            $('.' + id + '-label-group').fadeIn();
            showUsedFields();
        }

        function showUsedFields() {
            $('#label_field > option').each(function (key, option) {
                var isUsed = $('#labels_' + option.value).is(':visible');
                $(this).css('color', isUsed ? '#888' : 'black');
            });
        }

        $(function () {
            var options = {
                preferredFormat: 'hex',
                disabled: <?php echo Auth::user()->hasFeature(FEATURE_CUSTOMIZE_INVOICE_DESIGN) ? 'false' : 'true'; ?>,
                showInitial: false,
                showInput: true,
                allowEmpty: true,
                clickoutFiresChange: true,
            };

            $('#primary_color').spectrum(options);
            $('#secondary_color').spectrum(options);
            $('#header_font_id').change(function () {
                loadFont($('#header_font_id').val())
            });
            $('#body_font_id').change(function () {
                loadFont($('#body_font_id').val())
            });

            updateFieldLabels();
            refreshPDF();
            setTimeout(function () {
                showUsedFields();
            }, 1);

        });

    </script>


    <div class="row">
        <div class="col-md-12">

            <?php echo Former::open()->addClass('warn-on-exit')->onchange('if(!window.loadingFonts)refreshPDF()'); ?>


            <?php echo Former::populateField('invoice_design_id', $account->invoice_design_id); ?>

            <?php echo Former::populateField('quote_design_id', $account->quote_design_id); ?>

            <?php echo Former::populateField('body_font_id', $account->getBodyFontId()); ?>

            <?php echo Former::populateField('header_font_id', $account->getHeaderFontId()); ?>

            <?php echo Former::populateField('font_size', $account->font_size); ?>

            <?php echo Former::populateField('page_size', $account->page_size); ?>

            <?php echo Former::populateField('invoice_embed_documents', intval($account->invoice_embed_documents)); ?>

            <?php echo Former::populateField('primary_color', $account->primary_color); ?>

            <?php echo Former::populateField('secondary_color', $account->secondary_color); ?>

            <?php echo Former::populateField('hide_paid_to_date', intval($account->hide_paid_to_date)); ?>

            <?php echo Former::populateField('all_pages_header', intval($account->all_pages_header)); ?>

            <?php echo Former::populateField('all_pages_footer', intval($account->all_pages_footer)); ?>

            <?php echo Former::populateField('background_image_id', $account->background_image ? $account->background_image->public_id : null); ?>


            <?php $__currentLoopData = $invoiceLabels; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $field => $value): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                <?php echo Former::populateField("labels_{$field}", $value); ?>

            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>

            <div style="display:none">
                <?php echo Former::text('invoice_fields_json')->data_bind('value: ko.mapping.toJSON(model)'); ?>

            </div>

            <div class="panel panel-default">
                <div class="panel-heading" style="color: white;background-color: #777 !important;">
                    <h3 class="panel-title in-bold-white"><?php echo trans('texts.invoice_design'); ?></h3>
                </div>
                <div class="panel-body">
                    <div role="tabpanel">
                        <ul class="nav nav-tabs" role="tablist" style="border: none">
                            <li role="presentation" class="active"><a href="#general_settings"
                                                                      aria-controls="general_settings" role="tab"
                                                                      data-toggle="tab"><?php echo e(trans('texts.general_settings')); ?></a>
                            </li>
                            <li role="presentation"><a href="#invoice_labels" aria-controls="invoice_labels" role="tab"
                                                       data-toggle="tab"><?php echo e(trans('texts.invoice_labels')); ?></a></li>
                            <li role="presentation"><a href="#invoice_fields" aria-controls="invoice_fields" role="tab"
                                                       data-toggle="tab"><?php echo e(trans('texts.invoice_fields')); ?></a></li>
                            <li role="presentation"><a href="#product_fields" aria-controls="product_fields" role="tab"
                                                       data-toggle="tab"><?php echo e(trans('texts.product_fields')); ?></a></li>
                            <li role="presentation"><a href="#invoice_options" aria-controls="invoice_options"
                                                       role="tab"
                                                       data-toggle="tab"><?php echo e(trans('texts.invoice_options')); ?></a></li>
                        </ul>
                    </div>
                    <div class="tab-content">
                        <div role="tabpanel" class="tab-pane active" id="general_settings">
                            <div class="panel-body">
                                <div class="row">
                                    <div class="col-md-6">
                                        <?php echo Former::select('invoice_design_id')
                                                  ->label('invoice_design')
                                                ->fromQuery($invoiceDesigns, 'name', 'id'); ?>

                                        <?php echo Former::select('quote_design_id')
                                                  ->label('quote_design')
                                                ->fromQuery($invoiceDesigns, 'name', 'id'); ?>

                                        <?php echo Former::select('body_font_id')
                                                ->fromQuery($invoiceFonts, 'name', 'id'); ?>

                                        <?php echo Former::select('header_font_id')
                                                ->fromQuery($invoiceFonts, 'name', 'id'); ?>


                                    </div>
                                    <div class="col-md-6">
                                        <?php echo e(Former::setOption('TwitterBootstrap3.labelWidths.large', 6)); ?>

                                        <?php echo e(Former::setOption('TwitterBootstrap3.labelWidths.small', 6)); ?>

                                        <?php echo Former::select('page_size')
                                                ->options($pageSizes); ?>

                                        <?php echo Former::text('font_size')
                                              ->type('number')
                                              ->min('0')
                                              ->step('1'); ?>


                                        <?php echo Former::text('primary_color'); ?>

                                        <?php echo Former::text('secondary_color'); ?>


                                        <?php echo e(Former::setOption('TwitterBootstrap3.labelWidths.large', 4)); ?>

                                        <?php echo e(Former::setOption('TwitterBootstrap3.labelWidths.small', 4)); ?>


                                    </div>
                                </div>

                                <div class="help-block" style="padding-top:16px">
                                    <?php echo e(trans('texts.color_font_help')); ?>

                                </div>

                            </div>
                        </div>
                        <div role="tabpanel" class="tab-pane" id="invoice_labels">
                            <div class="panel-body">

                                <div class="row">
                                    <div class="col-md-6">
                                        <?php echo Former::select('label_field')
                                                ->placeholder('select_label')
                                                ->label('label')
                                                ->onchange('onFieldChange()')
                                                ->options(array_combine(App\Models\Account::$customLabels, Utils::trans(App\Models\Account::$customLabels))); ?>

                                    </div>
                                    <div class="col-md-6">
                                        <?php $__currentLoopData = App\Models\Account::$customLabels; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $field): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                            <?php echo Former::text('labels_' . $field)
                                                    ->label($field)
                                                    ->addGroupClass($field . '-label-group label-group'); ?>

                                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                    </div>
                                </div>

                            </div>
                        </div>
                        <div role="tabpanel" class="tab-pane" id="invoice_fields">
                            <div class="panel-body">
                                <div class="row" id="invoiceFields">
                                    <?php echo $__env->make('accounts.partials.invoice_fields_selector', ['section' => 'invoice_fields', 'fields' => INVOICE_FIELDS_INVOICE], array_except(get_defined_vars(), array('__data', '__path')))->render(); ?>
                                    <?php echo $__env->make('accounts.partials.invoice_fields_selector', ['section' => 'client_fields', 'fields' => INVOICE_FIELDS_CLIENT], array_except(get_defined_vars(), array('__data', '__path')))->render(); ?>
                                    <?php echo $__env->make('accounts.partials.invoice_fields_selector', ['section' => 'account_fields1', 'fields' => INVOICE_FIELDS_ACCOUNT], array_except(get_defined_vars(), array('__data', '__path')))->render(); ?>
                                    <?php echo $__env->make('accounts.partials.invoice_fields_selector', ['section' => 'account_fields2', 'fields' => INVOICE_FIELDS_ACCOUNT], array_except(get_defined_vars(), array('__data', '__path')))->render(); ?>
                                </div>
                                <div class="row" style="padding-top:30px">
                                    <div class="pull-left help-block">
                                        <?php echo e(trans('texts.invoice_fields_help')); ?>

                                    </div>
                                    <div class="pull-right" style="padding-right:14px">
                                        <?php echo Button::normal(trans('texts.reset'))->small()
                                              ->withAttributes(['onclick' => 'sweetConfirm(function() {
                                                  resetInvoiceFields();
                                              })']); ?>

                                    </div>
                                </div>
                            </div>
                        </div>
                        <div role="tabpanel" class="tab-pane" id="product_fields">
                            <div class="panel-body">
                                <div class="row" id="productFields">
                                    <?php echo $__env->make('accounts.partials.invoice_fields_selector', ['section' => 'product_fields', 'fields' => INVOICE_FIELDS_PRODUCT, 'colWidth' => 6], array_except(get_defined_vars(), array('__data', '__path')))->render(); ?>
                                    <?php echo $__env->make('accounts.partials.invoice_fields_selector', ['section' => 'task_fields', 'fields' => INVOICE_FIELDS_TASK, 'colWidth' => 6], array_except(get_defined_vars(), array('__data', '__path')))->render(); ?>
                                </div>
                                <div class="row" style="padding-top:30px">
                                    <div class="pull-left help-block">
                                        <?php echo e(trans('texts.product_fields_help')); ?>

                                    </div>
                                    <div class="pull-right" style="padding-right:14px">
                                        <?php echo Button::normal(trans('texts.reset'))->small()
                                              ->withAttributes(['onclick' => 'sweetConfirm(function() {
                                                  resetProductFields();
                                              })']); ?>

                                    </div>
                                </div>
                            </div>
                        </div>
                        <div role="tabpanel" class="tab-pane" id="invoice_options">
                            <div class="panel-body">
                                <?php if(auth()->user()->isEnterprise()): ?>
                                    <?php echo Former::select('background_image_id')
                                            ->label('background_image')
                                            ->addOption('', '')
                                            ->fromQuery(\App\Models\Document::scope()->proposalImages()->get(), function($model) { return $model->name . ' - ' . Utils::formatNumber($model->size / 1000, null, 1) . ' KB'; }, 'public_id')
                                            ->help($account->isModuleEnabled(ENTITY_PROPOSAL)
                                                    ? trans('texts.background_image_help', ['link' => link_to('/proposals/create?show_assets=true', trans('texts.proposal_editor'), ['target' => '_blank'])])
                                                    //: trans('texts.enable_proposals_for_background', ['link' => link_to('/settings/account_management', trans('texts.click_here'), ['target' => '_blank'])])
                                                    : 'To upload a background image <a href="http://www.ninja.test/settings/account_management" target="_blank">click here</a> to enable the proposals module.'
                                                ); ?>

                                <?php endif; ?>

                                <?php echo Former::checkbox('hide_paid_to_date')->text(trans('texts.hide_paid_to_date_help'))->value(1); ?>

                                <?php echo Former::checkbox('invoice_embed_documents')->text(trans('texts.invoice_embed_documents_help'))->value(1); ?>


                                <br/>

                                <?php echo Former::inline_radios('all_pages_header')
                                    ->label(trans('texts.all_pages_header'))
                                    ->radios([
                                        trans('texts.first_page') => ['value' => 0, 'name' => 'all_pages_header'],
                                        trans('texts.all_pages') => ['value' => 1, 'name' => 'all_pages_header'],
                                        ])->check($account->all_pages_header); ?>


                                <?php echo Former::inline_radios('all_pages_footer')
                                    ->label(trans('texts.all_pages_footer'))
                                    ->radios([
                                        trans('texts.last_page') => ['value' => 0, 'name' => 'all_pages_footer'],
                                        trans('texts.all_pages') => ['value' => 1, 'name' => 'all_pages_footer'],
                                        ])->check($account->all_pages_footer); ?>


                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <center class="buttons">
                <?php echo $account->getCustomDesign(CUSTOM_DESIGN1) ?
                        DropdownButton::primary(trans('texts.customize'))
                            ->withContents($account->present()->customDesigns)
                            ->large()  :
                        Button::primary(trans('texts.customize'))
                            ->appendIcon(Icon::create('edit'))
                            ->asLinkTo(URL::to('/settings/customize_design') . '?design_id=' . CUSTOM_DESIGN1)
                            ->large(); ?>

                <?php echo Auth::user()->hasFeature(FEATURE_CUSTOMIZE_INVOICE_DESIGN) ?
                        Button::success(trans('texts.save'))
                            ->submit()->large()
                            ->appendIcon(Icon::create('floppy-disk'))
                            ->withAttributes(['class' => 'save-button']) :
                        false; ?>

            </center>

            <?php echo Former::close(); ?>


        </div>
    </div>

    <?php echo $__env->make('invoices.pdf', ['account' => Auth::user()->account, 'pdfHeight' => 800], array_except(get_defined_vars(), array('__data', '__path')))->render(); ?>

<?php $__env->stopSection(); ?>

<?php echo $__env->make('header', array_except(get_defined_vars(), array('__data', '__path')))->render(); ?>