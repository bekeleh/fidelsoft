<?php

namespace App\Http\Requests;

use App\Models\Bill;

class BillRequest extends EntityRequest
{
    protected $entityType = ENTITY_BILL;

    public function authorize()
    {
        return true;
    }

    public function entity()
    {
        $Bill = parent::entity();

        // support loading an invoice by its invoice number
        if ($this->invoice_number && !$Bill) {
            $Bill = Bill::scope()
                ->where('invoice_number', $this->invoice_number)
                ->withTrashed()->first();

            if (!$Bill) {
                return response()->view('errors/403');
            }
        }
        // eager load the invoice items
        if ($Bill && !$Bill->relationLoaded('invoice_items')) {
            $Bill->load('invoice_items');
        }

        return $Bill;
    }
}
