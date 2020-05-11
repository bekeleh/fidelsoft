<?php $__env->startSection('content'); ?>

    <?php echo Former::open($url)
            ->addClass('col-lg-10 col-lg-offset-1 warn-on-exit')
            ->method($method)
            ->rules([
                'name' => 'required',
            ]); ?>


    <?php if($category): ?>
        <?php echo Former::populate($category); ?>

    <?php endif; ?>

    <span style="display:none">
        <?php echo Former::text('public_id'); ?>

    </span>

    <div class="row">
        <div class="col-lg-10 col-lg-offset-1">
            <div class="panel panel-default">
                <div class="panel-body">
                    <?php echo Former::text('name')->label('texts.expense_category_name'); ?>

                    <?php echo Former::text('notes')->label('texts.notes'); ?>

                </div>
            </div>

        </div>
    </div>


    <center class="buttons">
        <?php echo Button::normal(trans('texts.cancel'))->large()->asLinkTo(HTMLUtils::previousUrl('/expense_categories'))->appendIcon(Icon::create('remove-circle')); ?>

        <?php echo Button::success(trans('texts.save'))->submit()->large()->appendIcon(Icon::create('floppy-disk')); ?>

        <?php if($category && Auth::user()->can('create', ENTITY_EXPENSE)): ?>
            <?php echo Button::primary(trans('texts.new_expense'))->large()
                    ->asLinkTo(url("/expenses/create/0/0/{$category->public_id}"))
                    ->appendIcon(Icon::create('plus-sign')); ?>

        <?php endif; ?>
    </center>

    <?php echo Former::close(); ?>


    <script>
        $(function () {
            $('#name').focus();
        });
    </script>

<?php $__env->stopSection(); ?>

<?php echo $__env->make('header', array_except(get_defined_vars(), array('__data', '__path')))->render(); ?>