<?php

namespace App\Http\Requests;

class CreatePurchaseInvoiceAPIRequest extends PurchaseInvoiceRequest
{
    protected $entityType = ENTITY_PURCHASE_INVOICE;

    public function authorize()
    {
        return $this->user()->can('create', $this->entityType);
    }


    public function rules()
    {
        $rules = [
            'email' => 'required_without:vendor_id',
            'vendor_id' => 'required_without:email',
            'invoice_items' => 'valid_invoice_items',
            'invoice_number' => 'unique:purchase_invoices,invoice_number,,id,account_id,' . $this->user()->account_id,
            'discount' => 'positive',
            //'invoice_date' => 'date',
            //'due_date' => 'date',
            //'start_date' => 'date',
            //'end_date' => 'date',
        ];

        return $rules;
    }
}
