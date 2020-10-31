<div class="btn-group">
    <button type="button" class="btn btn-default btn-sm dropdown-toggle" data-toggle="dropdown">
        <div id="myAccountButton" class="ellipsis" style="width:85px">
            <?php echo $notif_cnt = count(Auth::user()->unreadnotifications) ?: '0' ?>
            Notifications <span class="badge"></span>
        </div>
    </button>
    <ul class="dropdown-menu">
        <?php if($notif_cnt): ?>
            <?php $__currentLoopData = $unreadMessages = auth()->user()->unreadnotifications; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $unreadMessage): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                <li>
                    <a href="<?php echo e($unreadMessage->data['link']); ?>?mark=unread">
                        <?php echo e($unreadMessage->data['title']); ?>

                        by <span class="fa fa-user"></span>&nbsp;&nbsp;<?php echo e(auth()->user()->username); ?>

                        <span class="fa fa-clock-o "></span>
                        <?php echo e(\Carbon\Carbon::parse($unreadMessage->data['created_at']['date'])->diffForHumans()); ?>

                    </a>
                </li>
            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
        <?php endif; ?>
    </ul>
</div>