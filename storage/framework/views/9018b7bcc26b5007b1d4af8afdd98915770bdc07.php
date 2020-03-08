<?php $__env->startSection('content'); ?>
    ##parent-placeholder-040f06fd774092478d450774f5ba30c5da78acc8##
<?php echo Former::open($url)
    ->method($method)
    ->autocomplete('off')
    ->rules(['name' => 'required|max:255'])
    ->addClass('col-lg-10 col-lg-offset-1 main-form warn-on-exit'); ?>


<?php if ($store): ?>
    <?php echo e(Former::populate($store)); ?>

<?php endif; ?>

    <span style="display:none">
        <?php echo Former::text('public_id'); ?>

        <?php echo Former::text('action'); ?>

    </span>

    <div class="row">
        <div class="col-lg-10 col-lg-offset-1">
            <div class="panel panel-default">
                <div class="panel-body form-padding-right">
                    <?php echo Former::text('name')->label('texts.name'); ?>

                    <?php echo Former::text('store_code')->label('texts.code'); ?>

                    <?php echo Former::textarea('notes')->rows(6); ?>

                </div>
            </div>
        </div>
    </div>

<?php $__currentLoopData = Module::getOrdered();
$__env->addLoop($__currentLoopData);
foreach ($__currentLoopData as $module): $__env->incrementLoopIndices();
    $loop = $__env->getLastLoop(); ?>
    <?php if (View::exists($module->alias . '::stores.edit')): ?>
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
                        <?php if ($__env->exists($module->alias . '::stores.edit')) echo $__env->make($module->alias . '::stores.edit', array_except(get_defined_vars(), array('__data', '__path')))->render(); ?>
                    </div>
                </div>
            </div>
        </div>
    <?php endif; ?>
<?php endforeach;
$__env->popLoop();
$loop = $__env->getLastLoop(); ?>
<?php if (Auth::user()->canCreateOrEdit(ENTITY_STORE, $store)): ?>
    <center class="buttons">
        <?php echo Button::normal(trans('texts.cancel'))->large()->asLinkTo(HTMLUtils::previousUrl('/stores'))->appendIcon(Icon::create('remove-circle')); ?>

        <?php echo Button::success(trans('texts.save'))->submit()->large()->appendIcon(Icon::create('floppy-disk')); ?>

        <?php if ($store): ?>
            <?php echo DropdownButton::normal(trans('texts.more_actions'))
                ->withContents($store->present()->moreActions())
                ->large()
                ->dropup(); ?>

        <?php endif; ?>
    </center>
<?php endif; ?>
<?php echo Former::close(); ?>

    <script type="text/javascript">
        $(function () {
            $('#name').focus();
        });

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