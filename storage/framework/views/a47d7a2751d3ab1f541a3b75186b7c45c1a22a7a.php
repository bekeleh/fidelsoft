<script type="application/ld+json">
[
<?php if ($entityType == ENTITY_INVOICE): ?>
{
  "@context": "http://schema.org",
  "@type": "Invoice",
  "paymentStatus": "<?php echo e($invoice->present()->paymentStatus); ?>",
  <?php if ($invoice->due_date): ?>
  "paymentDue": "<?php echo e($invoice->due_date); ?>T00:00:00+00:00",
  <?php endif; ?>
  "provider": {
    "@type": "Organization",
    "name": "<?php echo e($account->getDisplayName()); ?>"
  },
  "broker": {
    "@type": "Organization",
    "name": "Fidel",
    "url": "<?php echo e(NINJA_WEB_URL); ?>"
  },
  "totalPaymentDue": {
    "@type": "PriceSpecification",
    "price": "<?php echo e($account->formatMoney(isset($payment) ? $payment->amount : $invoice->getRequestedAmount(), $client)); ?>"
  },
  "action": {
    "@type": "ViewAction",
    "url": "<?php echo e($link); ?>"
  }
},
<?php endif; ?>
{
  "@context": "http://schema.org",
  "@type": "EmailMessage",
  "action": {
    "@type": "ViewAction",
    "url": "<?php echo e($link); ?>",
    "name": <?php echo json_encode(trans("texts.view_{$entityType}")); ?>

  }
}
]

</script>
