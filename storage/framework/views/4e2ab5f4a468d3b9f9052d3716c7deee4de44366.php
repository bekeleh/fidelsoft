<!-- navigation option  -->
<li class="nav-<?php use App\Models\EntityModel;

echo e($option); ?> <?php echo e(Request::is("{$option}*") ? 'active' : ''); ?>">
    <?php if($option == 'settings'): ?>
        <a type="button" class="btn btn-default btn-sm pull-right"
           title="<?php echo e(Utils::getReadableUrl(request()->path())); ?>"
           href="<?php echo e(Utils::getDocsUrl(request()->path())); ?>" target="_blank">
            <i class="fa fa-info-circle" style="width:20px"></i>
        </a>
    <?php elseif($option == 'reports'): ?>
        <a type="button" class="btn btn-default btn-sm pull-right" title="<?php echo e(trans('texts.calendar')); ?>"
           href="<?php echo e(url('/reports/calendar')); ?>">
            <i class="fa fa-calendar" style="width:20px"></i>
        </a>
    <?php elseif(Auth::user()->can('create', $option) || Auth::user()->can('create', substr($option, 0, -1))): ?>
        <a type="button" class="btn btn-primary btn-sm pull-right"
           href="<?php echo e(url("/{$option}/create")); ?>">
            <i class="fa fa-plus-circle" style="width:20px" title="<?php echo e(trans('texts.create_new')); ?>"></i>
        </a>
    <?php elseif(Auth::user()->can('view', substr($option, 0, -1))): ?>
        <a type="button" class="btn btn-primary btn-sm pull-right"
           href="<?php echo e(url("/{$option}")); ?>">
            <i class="fa fa-eye" style="width:20px" title="<?php echo e(trans('texts.view_record')); ?>"></i>
        </a>
    <?php endif; ?>
    <a href="<?php echo e(url($option == 'recurring' ? 'recurring_invoices' : $option)); ?>"
       style="padding-top:6px; padding-bottom:6px"
       class="nav-link <?php echo e(Request::is("{$option}*") ? 'active' : ''); ?>">
        <i class="fa fa-<?php echo e(empty($icon) ? EntityModel::getIcon($option) : $icon); ?>"
           style="width:46px; padding-right:10px"></i>
        <?php echo e(($option == 'recurring_invoices') ? trans('texts.recurring') : mtrans($option)); ?>

        <?php echo Utils::isTrial() && in_array($option, ['reports']) ? '&nbsp;<sup>' . trans('texts.pro') . '</sup>' : ''; ?>

    </a>
</li>