<div class="col-md-6">
    <ul>
        <?php if($entityType === ENTITY_QUOTE): ?>
            <li>$quote.quoteNumber</li>
            <li>$quote.quoteDate</li>
            <li>$quote.validUntil</li>
        <?php elseif($entityType === ENTITY_INVOICE): ?>
            <li>$invoice.invoiceNumber</li>
            <li>$invoice.invoiceDate</li>
            <li>$invoice.dueDate</li>
        <?php endif; ?>
        <li>$<?php echo e($entityType); ?>.discount</li>
        <li>$<?php echo e($entityType); ?>.poNumber</li>
        <li>$<?php echo e($entityType); ?>.publicNotes</li>
        <li>$<?php echo e($entityType); ?>.amount</li>
        <li>$<?php echo e($entityType); ?>.terms</li>
        <li>$<?php echo e($entityType); ?>.footer</li>
        <li>$<?php echo e($entityType); ?>.partial</li>
        <li>$<?php echo e($entityType); ?>.partialDueDate</li>
        <?php if($account->customLabel('invoice1')): ?>
            <li>$<?php echo e($entityType); ?>.customValue1</li>
        <?php endif; ?>
        <?php if($account->customLabel('invoice2')): ?>
            <li>$<?php echo e($entityType); ?>.customValue2</li>
        <?php endif; ?>
        <?php if($account->customLabel('invoice_text1')): ?>
            <li>$<?php echo e($entityType); ?>.customTextValue1</li>
        <?php endif; ?>
        <?php if($account->customLabel('invoice_text2')): ?>
            <li>$<?php echo e($entityType); ?>.customTextValue2</li>
        <?php endif; ?>
    </ul>
    <ul>
        <li>$account.name</li>
        <li>$account.idNumber</li>
        <li>$account.vatNumber</li>
        <li>$account.address1</li>
        <li>$account.address2</li>
        <li>$account.city</li>
        <li>$account.state</li>
        <li>$account.postalCode</li>
        <li>$account.country.name</li>
        <li>$account.phone</li>
        <?php if($account->custom_label1): ?>
            <li>$account.customValue1</li>
        <?php endif; ?>
        <?php if($account->custom_label2): ?>
            <li>$account.customValue2</li>
        <?php endif; ?>
    </ul>
</ul>
</div>
<div class="col-md-6">
    <ul>
        <li>$client.name</li>
        <li>$client.idNumber</li>
        <li>$client.vatNumber</li>
        <li>$client.address1</li>
        <li>$client.address2</li>
        <li>$client.city</li>
        <li>$client.state</li>
        <li>$client.postalCode</li>
        <li>$client.country.name</li>
        <li>$client.phone</li>
        <li>$client.balance</li>
        <?php if($account->customLabel('client1')): ?>
            <li>$client.customValue1</li>
        <?php endif; ?>
        <?php if($account->customLabel('client2')): ?>
            <li>$client.customValue2</li>
        <?php endif; ?>
    </ul>
    <ul>
        <li>$contact.firstName</li>
        <li>$contact.lastName</li>
        <li>$contact.email</li>
        <li>$contact.phone</li>
        <?php if($account->customLabel('contact1')): ?>
            <li>$contact.customValue1</li>
        <?php endif; ?>
        <?php if($account->customLabel('contact2')): ?>
            <li>$contact.customValue2</li>
        <?php endif; ?>
    </ul>
</div>
