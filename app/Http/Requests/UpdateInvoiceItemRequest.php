<?php

namespace App\Http\Requests;

class UpdateInvoiceItemRequest extends InvoiceItemRequest
{
    protected $entityType = ENTITY_INVOICE_ITEM;

    public function authorize()
    {
        return $this->user()->can('edit', ENTITY_INVOICE_ITEM);
    }

    public function rules()
    {
        $rules = [];

        return $rules;
    }
}
