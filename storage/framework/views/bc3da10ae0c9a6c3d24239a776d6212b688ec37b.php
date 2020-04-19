<?php $__env->startSection('content'); ?>
    ##parent-placeholder-040f06fd774092478d450774f5ba30c5da78acc8##
    <?php echo Former::open($url)
    ->method($method)
    ->autocomplete('off')
    ->rules(['name' => 'required|max:255','allow_invoice' => 'required','notes' => 'required|max:255'])
    ->addClass('col-lg-10 col-lg-offset-1 main-form warn-on-exit'); ?>

    <?php if($holdReason): ?>
        <?php echo e(Former::populate($holdReason)); ?>

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
                    <?php echo Former::text('name')->label('texts.hold_reason'); ?>

                    <?php echo Former::select('allow_invoice')
                             ->placeholder(trans('texts.select_hold_reason'))
                             ->fromQuery(\App\Models\HoldReason::getSelectOptions(), 'name', 'id'); ?>

                    <?php echo Former::textarea('notes')->rows(6); ?>

                </div>
            </div>
        </div>
    </div>

    <?php $__currentLoopData = Module::getOrdered(); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $module): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
        <?php if(View::exists($module->alias . '::hold_reasons.edit')): ?>
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
                            <?php if ($__env->exists($module->alias . '::hold_reasons.edit')) echo $__env->make($module->alias . '::hold_reasons.edit', array_except(get_defined_vars(), array('__data', '__path')))->render(); ?>
                        </div>
                    </div>
                </div>
            </div>
        <?php endif; ?>
    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
    <?php if(Auth::user()->canCreateOrEdit(ENTITY_HOLD_REASON, $holdReason)): ?>
        <center class="buttons">
            <?php echo Button::normal(trans('texts.cancel'))->large()->asLinkTo(HTMLUtils::previousUrl('/hold_reasons'))->appendIcon(Icon::create('remove-circle')); ?>

            <?php echo Button::success(trans('texts.save'))->submit()->large()->appendIcon(Icon::create('floppy-disk')); ?>

            <?php if($holdReason): ?>
                <?php echo DropdownButton::normal(trans('texts.more_actions'))
                ->withContents($holdReason->present()->moreActions())
                ->large()
                ->dropup(); ?>

            <?php endif; ?>
        </center>
    <?php endif; ?>
    <?php echo Former::close(); ?>

<?php $__env->stopSection(); ?>

<?php echo $__env->make('header', array_except(get_defined_vars(), array('__data', '__path')))->render(); ?>