<script type="application/ld+json">
[
<?php if($entityType == ENTITY_INVOICE): ?>
{
"@context": "http://schema.org",
"@type": "EmailMessage",
"action": {
"@type": "ViewAction",
"url": "<?php echo e($invoiceLink); ?>",
"name": <?php echo json_encode(trans("texts.view_{$entityType}")); ?>

}
}
<?php else: ?>
{
"@context": "http://schema.org",
"@type": "EmailMessage",
"action": {
"@type": "ViewAction",
"url": "<?php echo e($billLink); ?>",
"name": <?php echo json_encode(trans("texts.view_{$entityType}")); ?>

}
}
<?php endif; ?>
]



</script>
