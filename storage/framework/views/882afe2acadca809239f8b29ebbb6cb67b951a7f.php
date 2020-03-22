<?php if($message == strip_tags($message)): ?>
    <div class="alert alert-warning custom-message"><?php echo nl2br(Utils::isNinja() ? HTMLUtils::sanitizeHTML($message) : $message); ?></div>
<?php else: ?>
    <?php echo Utils::isNinja() ? HTMLUtils::sanitizeHTML($message) : $message; ?>

<?php endif; ?>
