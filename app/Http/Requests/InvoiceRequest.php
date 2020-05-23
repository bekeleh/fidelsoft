<?php

namespace App\Http\Requests;

use App\Models\Invoice;

class InvoiceRequest extends EntityRequest
{
    protected $entityType = ENTITY_INVOICE;

    public function authorize()
    {
        return true;
    }

    public function entity()
    {
        $invoice = parent::entity();

        // support loading an invoice by its invoice number
        if ($this->invoice_number && !$invoice) {
            $invoice = Invoice::scope()
                ->whereInvoiceNumber($this->invoice_number)
                ->withTrashed()
                ->first();

            if (!$invoice) {
                return response()->view('errors/403');
            }
        }
        // eager load the invoice items
        if ($invoice && !$invoice->relationLoaded('invoice_items')) {
            $invoice->load('invoice_items');
        }

        return $invoice;
    }
}
