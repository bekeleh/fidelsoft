<?php $__env->startSection('content'); ?>

    <?php echo Former::open($url)
            ->addClass('col-lg-10 col-lg-offset-1 warn-on-exit')
            ->method($method)
            ->rules([
                'name' => 'required',
                'notes' => 'required',
            ]); ?>


    <?php if($scheduleCategory): ?>
        <?php echo Former::populate($scheduleCategory); ?>

    <?php endif; ?>

    <span style="display:none">
        <?php echo Former::text('public_id'); ?>

    </span>

    <div class="row">
        <div class="col-lg-10 col-lg-offset-1">
            <div class="panel panel-default">
                <div class="panel-body">
                    <?php echo Former::text('name')->label('texts.schedule_category_name'); ?>

                    <?php echo Former::text('text_color')->label('texts.text_color'); ?>

                    <?php echo Former::text('bg_color')->label('texts.bg_color'); ?>

                    <?php echo Former::textarea('notes')->rows(6)->label('texts.notes'); ?>

                </div>
            </div>
        </div>
    </div>

    <center class="buttons">
        <?php echo Button::normal(trans('texts.cancel'))->large()->asLinkTo(HTMLUtils::previousUrl('/schedule_categories'))->appendIcon(Icon::create('remove-circle')); ?>

        <?php echo Button::success(trans('texts.save'))->submit()->large()->appendIcon(Icon::create('floppy-disk')); ?>

    </center>

    <?php echo Former::close(); ?>


    <script>
        $(function () {
            $('#name').focus();
        });
    </script>

<?php $__env->stopSection(); ?>

<?php echo $__env->make('header', array_except(get_defined_vars(), array('__data', '__path')))->render(); ?>