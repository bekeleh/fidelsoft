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

        // support loading an bill by its bill number
        if ($this->bill_number && !$bill) {
            $bill = Bill::scope()
                ->where('bill_number', $this->bill_number)
                ->withTrashed()->first();

            if (!$bill) {
                return response()->view('errors/403');
            }
        }
        // eager load the bill items
        if ($bill && !$bill->relationLoaded('bill_items')) {
            $bill->load('bill_items');
        }

        return $bill;
    }
}
