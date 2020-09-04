<script type="application/ld+json">
[
@if($entityType == ENTITY_INVOICE || $entityType == ENTITY_QUOTE)
{
"@context": "http://schema.org",
"@type": "EmailMessage",
"action": {
"@type": "ViewAction",
"url": "{{ $invoiceLink }}",
"name": {!! json_encode(trans("texts.view_{$entityType}")) !!}
}
}
@else
{
"@context": "http://schema.org",
"@type": "EmailMessage",
"action": {
"@type": "ViewAction",
"url": "{{ $billLink }}",
"name": {!! json_encode(trans("texts.view_{$entityType}")) !!}
}
}
@endif
]




</script>
