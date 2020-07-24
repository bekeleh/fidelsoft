<?php

namespace App\Http\Requests;

class PurchaseInvoiceItemRequest extends EntityRequest
{
    protected $entityType = ENTITY_PURCHASE_INVOICE_ITEM;

    public function authorize()
    {
        return true;
    }
}
