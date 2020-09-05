<script type="application/ld+json">
[
<?php if($entityType == ENTITY_INVOICE || $entityType == ENTITY_QUOTE): ?>
{
"@context": "http://schema.org",
"@type": "Invoice",
"paymentStatus": "<?php echo e($invoice->present()->paymentStatus); ?>",
<?php if(isset($invoice->due_date)): ?>
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
<?php elseif($entityType == ENTITY_BILL): ?>
{
"@context": "http://schema.org",
"@type": "Bill",
"paymentStatus": "<?php echo e($bill->present()->paymentStatus); ?>",
<?php if(isset($bill->due_date)): ?>
"paymentDue": "<?php echo e($bill->due_date); ?>T00:00:00+00:00",
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
"price": "<?php echo e($account->formatMoney(isset($payment) ? $payment->amount : $bill->getRequestedAmount(), $vendor)); ?>"
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
