<?php

namespace App\Http\Requests;

class CreateInvoiceAPIRequest extends InvoiceRequest
{
    protected $entityType = ENTITY_INVOICE;

    public function authorize()
    {
        return $this->user()->can('create', $this->entityType);
    }

    public function rules()
    {
        $rules = [
            'email' => 'required_without:client_id',
            'client_id' => 'required_without:email',
            'invoice_items' => 'valid_invoice_items',
            'invoice_number' => 'unique:invoices,invoice_number,,id,account_id,' . $this->user()->account_id,
            'discount' => 'positive',
            //'invoice_date' => 'date',
            //'due_date' => 'date',
            //'start_date' => 'date',
            //'end_date' => 'date',
        ];

        return $rules;
    }
}
