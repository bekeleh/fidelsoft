<?php

namespace App\Http\Requests;

use App\Models\PurchaseInvoice;

class PurchaseInvoiceRequest extends EntityRequest
{
    protected $entityType = ENTITY_PURCHASE_INVOICE;

    public function authorize()
    {
        return true;
    }

    public function entity()
    {
        $purchaseInvoice = parent::entity();

        // support loading an invoice by its invoice number
        if ($this->invoice_number && !$purchaseInvoice) {
            $purchaseInvoice = PurchaseInvoice::scope()
                ->where('invoice_number', $this->invoice_number)
                ->withTrashed()->first();

            if (!$purchaseInvoice) {
                return response()->view('errors/403');
            }
        }
        // eager load the invoice items
        if ($purchaseInvoice && !$purchaseInvoice->relationLoaded('invoice_items')) {
            $purchaseInvoice->load('invoice_items');
        }

        return $purchaseInvoice;
    }
}
