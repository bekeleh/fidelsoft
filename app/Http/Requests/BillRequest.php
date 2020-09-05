<?php

namespace App\Http\Requests;

use App\Models\Bill;
use Illuminate\Http\Response;

class BillRequest extends EntityRequest
{
    protected $entityType = ENTITY_BILL;

    public function authorize()
    {
        return true;
    }

    /**
     * @return Response|null
     */
    public function entity()
    {
        $bill = parent::entity();

        // support loading an Bill by its bill number
        if ($this->invoice_number && !$bill) {
            $bill = Bill::scope()->where('invoice_number', $this->invoice_number)
                ->withTrashed()->first();

            if (!$bill) {
                return response()->view('errors/403');
            }
        }
        // eager load the bill items
        if ($bill && !$bill->relationLoaded('invoice_items')) {
            $bill->load('invoice_items');
        }

        return $bill;
    }
}
