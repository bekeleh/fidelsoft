<div class="btn-group user-dropdown">
    <button type="button" class="btn btn-default btn-sm dropdown-toggle" data-toggle="dropdown">
        <div id="myAccountButton" class="ellipsis" style="width:60px;;max-width:80px;">
            <?php echo e(trans('texts.utility')); ?><span class="caret"></span>
        </div>
    </button>
    <ul class="dropdown-menu user-accounts">
        <li class="divider"></li>
        <li>
            <a href="javascript:showKeyboardShortcuts()" title="<?php echo e(trans('texts.help')); ?>">
                <?php echo e(trans('texts.help')); ?> <i class="fa fa-question-circle"></i>
            </a>
            <?php if(Auth::check()): ?>
                <a href="javascript:showContactUs()" title="<?php echo e(trans('texts.contact_us')); ?>">
                    <?php echo e(trans('texts.contact_us')); ?> <i class="fa fa-envelope"></i>
                </a>
            <?php endif; ?>
            <?php if(Auth::check() && !Auth::user()->registered): ?>
                <?php echo Button::success(trans('texts.sign_up'))->withAttributes(array('id' => 'signUpButton', 'onclick' => 'showSignUp()', 'style' => 'max-width:100px;;overflow:hidden'))->small(); ?>

            <?php endif; ?>
            <?php if(Auth::check() && Utils::isNinjaProd() && (!Auth::user()->isPro() || Auth::user()->isTrial())): ?>
                <?php if(Auth::user()->account->company->hasActivePromo()): ?>
                    <?php echo Button::warning(trans('texts.plan_upgrade'))->withAttributes(array('onclick' => 'showUpgradeModal()', 'style' => 'max-width:100px;overflow:hidden'))->small(); ?>

                <?php else: ?>
                    <?php echo Button::success(trans('texts.plan_upgrade'))->withAttributes(array('onclick' => 'showUpgradeModal()', 'style' => 'max-width:100px;overflow:hidden'))->small(); ?>

                <?php endif; ?>
            <?php endif; ?>
        </li>
    </ul>
</div>