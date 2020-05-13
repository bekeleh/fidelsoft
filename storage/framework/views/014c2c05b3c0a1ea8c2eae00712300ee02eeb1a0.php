<?php $__env->startSection('head'); ?>
    ##parent-placeholder-1a954628a960aaef81d7b2d4521929579f3541e6##

    <?php echo $__env->make('proposals.grapesjs_header', array_except(get_defined_vars(), array('__data', '__path')))->render(); ?>

<?php $__env->stopSection(); ?>

<?php $__env->startSection('content'); ?>

    <?php echo Former::open($url)
            ->method($method)
            ->onsubmit('return onFormSubmit(event)')
            ->addClass('warn-on-exit')
            ->rules([
                'name' => 'required',
            ]); ?>


    <?php if($template): ?>
        <?php echo Former::populate($template); ?>

    <?php endif; ?>

    <span style="display:none">
        <?php echo Former::text('public_id'); ?>

        <?php echo Former::text('html'); ?>

        <?php echo Former::text('css'); ?>

    </span>

    <div class="row">
		<div class="col-lg-12">
            <div class="panel panel-default">
            <div class="panel-body">
                <div class="row">
                    <div class="col-md-6">
                        <?php echo Former::text('name'); ?>

                    </div>
                    <div class="col-md-6">
                        <?php echo Former::textarea('private_notes'); ?>

                    </div>
                </div>
            </div>
            </div>
        </div>
    </div>

    <center class="buttons">

        <?php if(count($templateOptions)): ?>
            <?php echo Former::select()
                    ->style('display:inline;width:170px;background-color:white !important')
                    ->placeholder(trans('texts.load_template'))
                    ->onchange('onTemplateSelectChange()')
                    ->addClass('template-select')
                    ->options($templateOptions)
                    ->raw(); ?>

        <?php endif; ?>

        <?php echo $__env->make('proposals.grapesjs_help', array_except(get_defined_vars(), array('__data', '__path')))->render(); ?>

        <?php echo Button::normal(trans('texts.cancel'))
                ->appendIcon(Icon::create('remove-circle'))
                ->asLinkTo(HTMLUtils::previousUrl('/proposals')); ?>


        <?php echo Button::success(trans('texts.save'))
                ->submit()
                ->appendIcon(Icon::create('floppy-disk')); ?>


        <?php if($template): ?>
            <?php echo Button::primary(trans('texts.new_proposal'))
                    ->appendIcon(Icon::create('plus-sign'))
                    ->asLinkTo(url('/proposals/create/0/' . $template->public_id)); ?>

        <?php endif; ?>

    </center>

    <?php echo Former::close(); ?>


    <div id="gjs"></div>

    <script type="text/javascript">
    var customTemplates = <?php echo $customTemplates; ?>;
    var customTemplateMap = {};

    var defaultTemplates = <?php echo $defaultTemplates; ?>;
    var defaultTemplateMap = {};

    function onFormSubmit() {
        $('#html').val(grapesjsEditor.getHtml());
        $('#css').val(grapesjsEditor.getCss());

        return true;
    }

    function onTemplateSelectChange() {
        var templateId = $('.template-select').val();
        var group = $('.template-select :selected').parent().attr('label');

        if (group == "<?php echo e(trans('texts.default')); ?>") {
            var template = defaultTemplateMap[templateId];
        } else {
            var template = customTemplateMap[templateId];
        }

        grapesjsEditor.CssComposer.getAll().reset();
        grapesjsEditor.setComponents(template.html);
        grapesjsEditor.setStyle(template.css);

        $('.template-select').val(null).blur();
    }

    $(function() {
        for (var i=0; i<customTemplates.length; i++) {
            var template = customTemplates[i];
            customTemplateMap[template.public_id] = template;
        }
        for (var i=0; i<defaultTemplates.length; i++) {
            var template = defaultTemplates[i];
            defaultTemplateMap[template.public_id] = template;
        }
    })

</script>

<?php echo $__env->make('proposals.grapesjs', ['entity' => $template], array_except(get_defined_vars(), array('__data', '__path')))->render(); ?>

<?php $__env->stopSection(); ?>

<?php echo $__env->make('header', array_except(get_defined_vars(), array('__data', '__path')))->render(); ?>