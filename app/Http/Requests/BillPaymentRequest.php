<?php

namespace App\Http\Requests;

class BillPaymentRequest extends EntityRequest
{
    protected $entityType = ENTITY_BILL_PAYMENT;

    public function authorize()
    {
        return true;
    }

}
