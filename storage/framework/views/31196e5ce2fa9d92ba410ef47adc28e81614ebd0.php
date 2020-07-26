<div class="btn-group user-dropdown">
    <button type="button" class="btn btn-default btn-sm dropdown-toggle" data-toggle="dropdown">
        <div id="myAccountButton" class="ellipsis"
             style="max-width:<?php echo e(Utils::hasFeature(FEATURE_USERS) ? '1100' : '100'); ?>px;">
            <?php if(session(SESSION_USER_ACCOUNTS) && count(session(SESSION_USER_ACCOUNTS))): ?>
                <?php echo e(Auth::user()->account->getDisplayName()); ?>

            <?php else: ?>
                <?php echo e(Auth::user()->getDisplayName()); ?>

            <?php endif; ?>
            <span class="caret"></span>
        </div>
    </button>
    <ul class="dropdown-menu user-accounts">
        <?php if(session(SESSION_USER_ACCOUNTS)): ?>
            <?php $__currentLoopData = session(SESSION_USER_ACCOUNTS); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $item): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                <?php if($item->user_id == Auth::user()->id): ?>
                    <?php echo $__env->make('user_account', [
                    'user_account_id' => $item->id,
                    'user_id' => $item->user_id,
                    'account_name' => $item->account_name,
                    'user_name' => $item->user_name,
                    'logo_url' => isset($item->logo_url) ? $item->logo_url : "",
                    'selected' => true,
                    ], array_except(get_defined_vars(), array('__data', '__path')))->render(); ?>
                <?php endif; ?>
            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
            <?php if(Utils::isSuperUser()): ?>
                <?php $__currentLoopData = session(SESSION_USER_ACCOUNTS); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $item): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                    <?php if($item->user_id != Auth::user()->id): ?>
                        <?php echo $__env->make('user_account', [
                        'user_account_id' => $item->id,
                        'user_id' => $item->user_id,
                        'account_name' => $item->account_name,
                        'user_name' => $item->user_name,
                        'logo_url' => isset($item->logo_url) ? $item->logo_url : "",
                        'selected' => false,
                        ], array_except(get_defined_vars(), array('__data', '__path')))->render(); ?>
                    <?php endif; ?>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
            <?php endif; ?>
        <?php else: ?>
            <?php echo $__env->make('user_account', [
            'account_name' => Auth::user()->account->name ?: trans('texts.untitled'),
            'user_name' => Auth::user()->getDisplayName(),
            'logo_url' => Auth::user()->account->getLogoURL(),
            'selected' => true,
            ], array_except(get_defined_vars(), array('__data', '__path')))->render(); ?>
        <?php endif; ?>
        <li class="divider"></li>
        <?php if(Utils::isSuperUser() && Auth::user()->confirmed && Utils::getResllerType() != RESELLER_ACCOUNT_COUNT): ?>
            <?php if(!session(SESSION_USER_ACCOUNTS) || count(session(SESSION_USER_ACCOUNTS)) < 5): ?>
                <li><?php echo link_to('#', trans('texts.add_company'), ['onclick' => 'showSignUp()']); ?></li>
            <?php endif; ?>
        <?php endif; ?>
        <li>
            <?php echo link_to('#', trans('texts.logout'), array('onclick'=>'logout()')); ?>

        </li>
    </ul>
</div>