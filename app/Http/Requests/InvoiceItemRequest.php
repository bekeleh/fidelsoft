<?php

namespace App\Http\Requests;

class InvoiceItemRequest extends EntityRequest
{
    protected $entityType = ENTITY_INVOICE_ITEM;

    public function authorize()
    {
        return true;
    }
}
