<?php

namespace App\Http\Requests;

class UpdatePurchaseInvoiceItemRequest extends PurchaseInvoiceItemRequest
{
    protected $entityType = ENTITY_PURCHASE_INVOICE_ITEM;

    public function authorize()
    {
        return $this->user()->can('edit', $this->entityType);
    }

    public function rules()
    {
        $rules = [];

        return $rules;
    }
}
