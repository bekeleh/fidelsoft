<?php

namespace App\Http\Requests;

class CreatePurchaseInvoiceItemRequest extends PurchaseInvoiceItemRequest
{
    protected $entityType = ENTITY_PURCHASE_INVOICE_ITEM;

    public function authorize()
    {
        return $this->user()->can('create', $this->entityType);
    }

    public function rules()
    {
        $rules = [];

        return $rules;
    }
}
