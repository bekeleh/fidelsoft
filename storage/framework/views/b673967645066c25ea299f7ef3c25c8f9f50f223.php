<div class="btn-group">
    <button type="button" class="btn btn-default btn-sm dropdown-toggle" data-toggle="dropdown">
        <div id="myAccountButton" class="ellipsis" style="width:85px">
            <?php echo $notif_cnt = count(Auth::user()->unreadnotifications) ?: '0' ?>
            Notifications <span class="badge"></span>
        </div>
    </button>
    <ul class="dropdown-menu">
        <?php if($notif_cnt): ?>
            <?php $__currentLoopData = $links =Auth::user()->unreadnotifications; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $link): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                <li>
                    <a href="<?php echo e($link->data['link']); ?>">
                        <span class="fa fa-file-pdf-o"></span>
                        <?php echo e($link->data['title']); ?> <?php echo e(\Carbon\Carbon::parse($link->posted_at)->diffForHumans()); ?>

                    </a>
                </li>
            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
        <?php endif; ?>
    </ul>
</div>