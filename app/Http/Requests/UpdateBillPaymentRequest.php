<?php

namespace App\Http\Requests;

class UpdateBillPaymentRequest extends BillPaymentRequest
{
    protected $entityType = ENTITY_BILL_PAYMENT;

    public function authorize()
    {
        return $this->user()->can('edit', $this->entityType);
    }

    public function rules()
    {
        return [];
    }
}
