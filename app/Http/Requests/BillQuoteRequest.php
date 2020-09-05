<?php

namespace App\Http\Requests;

use App\Models\Bill;

class BillQuoteRequest extends EntityRequest
{
    protected $entityType = ENTITY_BILL_QUOTE;

    public function authorize()
    {
        return true;
    }

    public function entity()
    {
        $bill = parent::entity();

        // support loading an invoice by its invoice number
        if ($this->invoice_number && !$bill) {
            $bill = Bill::scope()->where('invoice_number', $this->invoice_number)->withTrashed()->first();
            if (!$bill) {
                abort(404);
            }
        }
        // eager load the invoice items
        if ($bill && !$bill->relationLoaded('invoice_items')) {
            $bill->load('invoice_items');
        }

        return $bill;
    }
}
