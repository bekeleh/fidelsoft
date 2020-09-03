<script type="application/ld+json">
[
{
  "@context": "http://schema.org",
  "@type": "EmailMessage",
  "action": {
    "@type": "ViewAction",
    "url": "<?php echo e($invoiceLink); ?>",
    "name": <?php echo json_encode(trans("texts.view_{$entityType}")); ?>

  }
}
]
</script>
