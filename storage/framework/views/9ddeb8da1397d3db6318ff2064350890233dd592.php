<?php if($account->hasLogo()): ?>
    <?php if($account->website): ?>
        <a href="<?php echo e($account->website); ?>" style="color: #19BB40; text-decoration: underline;">
    <?php endif; ?>

    <img src="<?php echo e(isset($message) ? $message->embed($account->getLogoPath()) : 'cid:' . $account->getLogoName()); ?>" height="50" style="height:50px; max-width:140px; margin-left: 33px; padding-top: 2px" alt=""/>

    <?php if($account->website): ?>
        </a>
    <?php endif; ?>
<?php endif; ?>
