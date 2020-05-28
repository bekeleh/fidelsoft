<?php $__env->startSection('head'); ?>
    ##parent-placeholder-1a954628a960aaef81d7b2d4521929579f3541e6##

    <?php echo $__env->make('money_script', array_except(get_defined_vars(), array('__data', '__path')))->render(); ?>
    <link href="<?php echo e(asset('css/quill.snow.css')); ?>" rel="stylesheet" type="text/css"/>
    <script src="<?php echo e(asset('js/quill.min.js')); ?>" type="text/javascript"></script>

    <style type="text/css">
        textarea {
            min-height: 150px !important;
        }
    </style>

    <script type="text/javascript">
        var editors = [];
    </script>

<?php $__env->stopSection(); ?>

<?php $__env->startSection('content'); ?>
    ##parent-placeholder-040f06fd774092478d450774f5ba30c5da78acc8##
    <?php echo $__env->make('accounts.nav', ['selected' => ACCOUNT_TEMPLATES_AND_REMINDERS, 'advanced' => true], array_except(get_defined_vars(), array('__data', '__path')))->render(); ?>


    <?php echo Former::vertical_open()->addClass('warn-on-exit'); ?>


    <?php $__currentLoopData = App\Models\AccountEmailSettings::$templates; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $type): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
        <?php $__currentLoopData = ['subject', 'template']; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $field): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
            <?php echo e(Former::populateField("email_{$field}_{$type}", $templates[$type][$field])); ?>

        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>

    <?php $__currentLoopData = [TEMPLATE_REMINDER1, TEMPLATE_REMINDER2, TEMPLATE_REMINDER3]; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $type): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
        <?php $__currentLoopData = ['enable', 'num_days', 'direction', 'field']; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $field): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
            <?php echo e(Former::populateField("{$field}_{$type}", $account->{"{$field}_{$type}"})); ?>

        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>

    <div class="panel panel-default">
        <div class="panel-heading" style="color:white;background-color: #777 !important;">
            <h3 class="panel-title in-bold-white">
                <?php echo trans('texts.email_templates'); ?></h3>
        </div>
        <div class="panel-body">
            <div class="row">
                <div role="tabpanel">
                    <ul class="nav nav-tabs" role="tablist" style="border: none">
                        <li role="presentation" class="active"><a href="#invoice" aria-controls="notes" role="tab"
                                                                  data-toggle="tab"><?php echo e(trans('texts.invoice_email')); ?></a>
                        </li>
                        <li role="presentation"><a href="#quote" aria-controls="terms" role="tab"
                                                   data-toggle="tab"><?php echo e(trans('texts.quote_email')); ?></a></li>
                        <li role="presentation"><a href="#proposal" aria-controls="terms" role="tab"
                                                   data-toggle="tab"><?php echo e(trans('texts.proposal_email')); ?></a></li>
                        <li role="presentation"><a href="#payment" aria-controls="footer" role="tab"
                                                   data-toggle="tab"><?php echo e(trans('texts.payment_email')); ?></a></li>
                    </ul>
                    <div class="tab-content">
                        <?php echo $__env->make('accounts.template', ['field' => 'invoice', 'active' => true], array_except(get_defined_vars(), array('__data', '__path')))->render(); ?>
                        <?php echo $__env->make('accounts.template', ['field' => 'quote'], array_except(get_defined_vars(), array('__data', '__path')))->render(); ?>
                        <?php echo $__env->make('accounts.template', ['field' => 'proposal'], array_except(get_defined_vars(), array('__data', '__path')))->render(); ?>
                        <?php echo $__env->make('accounts.template', ['field' => 'payment'], array_except(get_defined_vars(), array('__data', '__path')))->render(); ?>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <p>&nbsp;</p>

    <div class="panel panel-default">
        <div class="panel-heading">
            <h3 class="panel-title"><?php echo trans('texts.reminder_emails'); ?></h3>
        </div>
        <div class="panel-body">
            <div class="row">
                <div role="tabpanel">
                    <ul class="nav nav-tabs" role="tablist" style="border: none">
                        <li role="presentation" class="active"><a href="#reminder1" aria-controls="notes" role="tab"
                                                                  data-toggle="tab"><?php echo e(trans('texts.first_reminder')); ?></a>
                        </li>
                        <li role="presentation"><a href="#reminder2" aria-controls="terms" role="tab"
                                                   data-toggle="tab"><?php echo e(trans('texts.second_reminder')); ?></a></li>
                        <li role="presentation"><a href="#reminder3" aria-controls="footer" role="tab"
                                                   data-toggle="tab"><?php echo e(trans('texts.third_reminder')); ?></a></li>
                        <li role="presentation"><a href="#reminder4" aria-controls="footer" role="tab"
                                                   data-toggle="tab"><?php echo e(trans('texts.endless_reminder')); ?></a></li>
                    </ul>
                    <div class="tab-content">
                        <?php echo $__env->make('accounts.template', ['field' => 'reminder1', 'number' => 1, 'isReminder' => true, 'active' => true], array_except(get_defined_vars(), array('__data', '__path')))->render(); ?>
                        <?php echo $__env->make('accounts.template', ['field' => 'reminder2', 'number' => 2, 'isReminder' => true], array_except(get_defined_vars(), array('__data', '__path')))->render(); ?>
                        <?php echo $__env->make('accounts.template', ['field' => 'reminder3', 'number' => 3, 'isReminder' => true], array_except(get_defined_vars(), array('__data', '__path')))->render(); ?>
                        <?php echo $__env->make('accounts.template', ['field' => 'reminder4', 'number' => 4, 'isReminder' => true], array_except(get_defined_vars(), array('__data', '__path')))->render(); ?>
                    </div>
                </div>
            </div>
        </div>
    </div>


    <div class="modal fade" id="templatePreviewModal" tabindex="-1" role="dialog"
         aria-labelledby="templatePreviewModalLabel" aria-hidden="true">
        <div class="modal-dialog" style="width:800px">
            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
                    <h4 class="modal-title" id="templatePreviewModalLabel"><?php echo e(trans('texts.preview')); ?></h4>
                </div>

                <div class="container" style="width: 100%; padding-bottom: 0px !important">
                    <div class="panel panel-default">
                        <div class="panel-body">
                            <iframe id="server-preview" style="background-color:#FFFFFF" frameborder="1" width="100%"
                                    height="500px"/>
                            </iframe>
                        </div>
                    </div>
                </div>

                <div class="modal-footer">
                    <button type="button" class="btn btn-primary"
                            data-dismiss="modal"><?php echo e(trans('texts.close')); ?></button>
                </div>
            </div>
        </div>
    </div>

    <div class="modal fade" id="rawModal" tabindex="-1" role="dialog" aria-labelledby="rawModalLabel"
         aria-hidden="true">
        <div class="modal-dialog" style="width:800px">
            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
                    <h4 class="modal-title" id="rawModalLabel"><?php echo e(trans('texts.raw_html')); ?></h4>
                </div>

                <div class="container" style="width: 100%; padding-bottom: 0px !important">
                    <div class="panel panel-default">
                        <div class="modal-body">
                            <textarea id="raw-textarea" rows="20" style="width:100%"></textarea>
                        </div>
                    </div>
                </div>

                <div class="modal-footer">
                    <button type="button" class="btn btn-default"
                            data-dismiss="modal"><?php echo e(trans('texts.close')); ?></button>
                    <button type="button" onclick="updateRaw()" class="btn btn-success"
                            data-dismiss="modal"><?php echo e(trans('texts.update')); ?></button>
                </div>
            </div>
        </div>
    </div>

    <?php if(Auth::user()->hasFeature(FEATURE_EMAIL_TEMPLATES_REMINDERS)): ?>
        <center>
            <?php echo Button::success(trans('texts.save'))->submit()->large()->appendIcon(Icon::create('floppy-disk')); ?>

        </center>
    <?php else: ?>
        <script>
            $(function () {
                $('form.warn-on-exit input').prop('disabled', true);
            });
        </script>
    <?php endif; ?>

    <?php echo Former::close(); ?>


    <script type="text/javascript">

        var entityTypes = <?php echo json_encode(App\Models\AccountEmailSettings::$templates); ?>;
        var stringTypes = ['subject', 'template'];
        var templates = <?php echo json_encode($defaultTemplates); ?>;
        var account = <?php echo Auth::user()->account; ?>;

        function refreshPreview() {
            for (var i = 0; i < entityTypes.length; i++) {
                var entityType = entityTypes[i];
                for (var j = 0; j < stringTypes.length; j++) {
                    var stringType = stringTypes[j];
                    var idName = '#email_' + stringType + '_' + entityType;
                    var value = $(idName).val();
                    var previewName = '#' + entityType + '_' + stringType + '_preview';
                    $(previewName).html(renderEmailTemplate(value, false, entityType));
                }
            }
        }

        function serverPreview(field) {
            $('#templatePreviewModal').modal('show');
            var template = $('#email_template_' + field).val();
            var url = '<?php echo e(URL::to('settings/email_preview')); ?>?template=' + template;
            $('#server-preview').attr('src', url).load(function () {
                // disable links in the preview
                $('iframe').contents().find('a').each(function (index) {
                    $(this).on('click', function (event) {
                        event.preventDefault();
                        event.stopPropagation();
                    });
                });
            });
        }

        $(function () {
            for (var i = 0; i < entityTypes.length; i++) {
                var entityType = entityTypes[i];
                for (var j = 0; j < stringTypes.length; j++) {
                    var stringType = stringTypes[j];
                    var idName = '#email_' + stringType + '_' + entityType;
                    $(idName).keyup(refreshPreview);
                }
            }

            $('.show-when-ready').show();

            refreshPreview();
        });

        function setDirectionShown(field) {
            var val = $('#field_' + field).val();
            if (val == <?php echo e(REMINDER_FIELD_INVOICE_DATE); ?>) {
                $('#days_after_' + field).show();
                $('#direction_' + field).hide();
            } else {
                $('#days_after_' + field).hide();
                $('#direction_' + field).show();
            }
        }

        function resetText(section, field) {
            sweetConfirm(function () {
                var fieldName = 'email_' + section + '_' + field;
                var value = templates[field][section];
                $('#' + fieldName).val(value);
                if (section == 'template') {
                    editors[field].setHTML(value);
                }
                refreshPreview();
            });
        }

        function showRaw(field) {
            window.rawHtmlField = field;
            var template = $('#email_template_' + field).val();
            $('#raw-textarea').val(formatXml(template));
            $('#rawModal').modal('show');
        }

        function updateRaw() {
            var value = $('#raw-textarea').val();
            var field = window.rawHtmlField;
            editors[field].setHTML(value);
            value = editors[field].getHTML();
            var fieldName = 'email_template_' + field;
            $('#' + fieldName).val(value);
            refreshPreview();
        }

    </script>

    <?php echo $__env->make('partials.email_templates', array_except(get_defined_vars(), array('__data', '__path')))->render(); ?>

<?php $__env->stopSection(); ?>

<?php echo $__env->make('header', array_except(get_defined_vars(), array('__data', '__path')))->render(); ?>