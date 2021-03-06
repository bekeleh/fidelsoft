<script type="application/ld+json">
[
@if ($entityType == ENTITY_INVOICE || $entityType == ENTITY_QUOTE)
{
"@context": "http://schema.org",
"@type": "Invoice",
"paymentStatus": "{{ $invoice->present()->paymentStatus }}",
@if (isset($invoice->due_date))
"paymentDue": "{{ $invoice->due_date }}T00:00:00+00:00",
@endif
"provider": {
"@type": "Organization",
"name": "{{ $account->getDisplayName() }}"
},
"broker": {
"@type": "Organization",
"name": "Fidel",
"url": "{{ NINJA_WEB_URL }}"
},
"totalPaymentDue": {
"@type": "PriceSpecification",
"price": "{{ $account->formatMoney(isset($payment) ? $payment->amount : $invoice->getRequestedAmount(), $client) }}"
},
"action": {
"@type": "ViewAction",
"url": "{{ $link }}"
}
},
@elseif ($entityType == ENTITY_BILL)
{
"@context": "http://schema.org",
"@type": "Bill",
"paymentStatus": "{{ $bill->present()->paymentStatus }}",
@if (isset($bill->due_date))
"paymentDue": "{{ $bill->due_date }}T00:00:00+00:00",
@endif
"provider": {
"@type": "Organization",
"name": "{{ $account->getDisplayName() }}"
},
"broker": {
"@type": "Organization",
"name": "Fidel",
"url": "{{ NINJA_WEB_URL }}"
},
"totalPaymentDue": {
"@type": "PriceSpecification",
"price": "{{ $account->formatMoney(isset($payment) ? $payment->amount : $bill->getRequestedAmount(), $vendor) }}"
},
"action": {
"@type": "ViewAction",
"url": "{{ $link }}"
}
},
@endif
{
"@context": "http://schema.org",
"@type": "EmailMessage",
"action": {
"@type": "ViewAction",
"url": "{{ $link }}",
"name": {!! json_encode(trans("texts.view_{$entityType}")) !!}
}
}
]
</script>
