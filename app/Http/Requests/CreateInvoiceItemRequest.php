<?php

namespace App\Http\Requests;

class CreateInvoiceItemRequest extends InvoiceItemRequest
{
    protected $entityType = ENTITY_INVOICE_ITEM;

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
