<?php $__env->startSection('content'); ?>
    ##parent-placeholder-040f06fd774092478d450774f5ba30c5da78acc8##
<?php echo Former::open($url)
    ->method($method)
    ->autocomplete('off')
    ->rules(['name' => 'required|max:50', 'notes' => 'required|max:255'])
    ->addClass('col-lg-10 col-lg-offset-1 main-form warn-on-exit'); ?>

<?php if ($unit): ?>
    <?php echo e(Former::populate($unit)); ?>

    <div style="display:none">
        <?php echo Former::text('public_id'); ?>

    </div>
<?php endif; ?>
    <span style="display:none">
<?php echo Former::text('public_id'); ?>

<?php echo Former::text('action'); ?>

</span>
    <div class="row">
        <div class="col-lg-10 col-lg-offset-1">
            <div class="panel panel-default">
                <div class="panel-body form-padding-right">
                    <?php echo Former::text('name')->label('texts.unit_name'); ?>

                    <?php echo Former::textarea('notes')->rows(6); ?>

                </div>
            </div>
        </div>
    </div>
<?php $__currentLoopData = Module::getOrdered();
$__env->addLoop($__currentLoopData);
foreach ($__currentLoopData as $module): $__env->incrementLoopIndices();
    $loop = $__env->getLastLoop(); ?>
    <?php if (View::exists($module->alias . '::units.edit')): ?>
        <div class="row">
            <div class="col-lg-10 col-lg-offset-1">
                <div class="panel panel-default">
                    <div class="panel-heading">
                        <h3 class="panel-title in-white">
                            <i class="fa fa-<?php echo e($module->icon); ?>"></i>
                            <?php echo e($module->name); ?>

                        </h3>
                    </div>
                    <div class="panel-body form-padding-right">
                        <?php if ($__env->exists($module->alias . '::units.edit')) echo $__env->make($module->alias . '::units.edit', array_except(get_defined_vars(), array('__data', '__path')))->render(); ?>
                    </div>
                </div>
            </div>
        </div>
    <?php endif; ?>
<?php endforeach;
$__env->popLoop();
$loop = $__env->getLastLoop(); ?>
<?php if (Auth::user()->canCreateOrEdit(ENTITY_UNIT, $unit)): ?>
    <center class="buttons">
        <?php echo Button::normal(trans('texts.cancel'))->large()->asLinkTo(HTMLUtils::previousUrl('/units'))->appendIcon(Icon::create('remove-circle')); ?>

        <?php echo Button::success(trans('texts.save'))->submit()->large()->appendIcon(Icon::create('floppy-disk')); ?>

        <?php if ($unit): ?>
            <?php echo DropdownButton::normal(trans('texts.more_actions'))
                ->withContents($unit->present()->moreActions())
                ->large()
                ->dropup(); ?>

        <?php endif; ?>
    </center>
<?php endif; ?>
<?php echo Former::close(); ?>

    <script type="text/javascript">

        function submitAction(action) {
            $('#action').val(action);
            $('.main-form').submit();
        }

        function onDeleteClick() {
            sweetConfirm(function () {
                submitAction('delete');
            });
        }
    </script>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('header', array_except(get_defined_vars(), array('__data', '__path')))->render(); ?>