<!-- nav settings -->
<?php use App\Models\Common\Account;

if(!Utils::isPro() && isset($advanced) && $advanced): ?>
    <div class="alert alert-warning" style="font-size:larger;">
        <center>
            <?php echo trans('texts.pro_plan_advanced_settings', ['link'=>'<a href="javascript:showUpgradeModal()">' . trans('texts.pro_plan_remove_logo_link') . '</a>']); ?>

        </center>
    </div>
<?php endif; ?>

<script type="text/javascript">
    $(function () {
        if (isStorageSupported() && /\/settings\//.test(location.href)) {
            localStorage.setItem('last:settings_page', location.href);
        }

        $('a[data-toggle="tab"]').on('shown.bs.tab', function (e) {
            var target = $(e.target).attr("href"); // activated tab
            if (history.pushState) {
                history.pushState(null, null, target);
            }
        });

    })
</script>

<div class="row">
    <div class="col-md-3">
        <?php $__currentLoopData = [
            BASIC_SETTINGS => Account::$basicSettings,
            ADVANCED_SETTINGS => Account::$advancedSettings,
        ]; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $type => $settings): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
            <div class="panel panel-default">
                <div class="panel-heading" style="color:white;background-color: #777 !important;">
                    <h3 class="panel-title in-bold-white">
                        <?php echo e(trans("texts.{$type}")); ?>

                        <?php if($type === ADVANCED_SETTINGS && ! Utils::isPaidPro()): ?>
                            <sup><?php echo e(strtoupper(trans('texts.pro'))); ?></sup>
                        <?php endif; ?>
                    </h3>
                </div>
                <div class="list-group">
                    <?php $__currentLoopData = $settings; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $section): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                        <?php if($section != ACCOUNT_USER_DETAILS || auth()->user()->registered): ?>
                            <a href="<?php echo e(URL::to("settings/{$section}")); ?>"
                               class="list-group-item <?php echo e($selected === $section ? 'selected' : ''); ?>"
                               style="width:100%;text-align:left"><?php echo e(trans("texts.{$section}")); ?></a>
                        <?php endif; ?>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                    <?php if($type === ADVANCED_SETTINGS && !Utils::isNinjaProd() && auth()->user()->isSuperUser()): ?>
                        <a href="<?php echo e(URL::to("settings/system_settings")); ?>"
                           class="list-group-item <?php echo e($selected === 'system_settings' ? 'selected' : ''); ?>"
                           style="width:100%;text-align:left"><?php echo e(trans("texts.system_settings")); ?></a>
                    <?php endif; ?>
                </div>
            </div>
        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
    </div>
    <div class="col-md-9">
