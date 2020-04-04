<?php

namespace App\Http\Requests;

class UpdatePaymentRequest extends PaymentRequest
{
    protected $entityType = ENTITY_PAYMENT;

    public function authorize()
    {
        return true;
    }

    public function rules()
    {
        return [];
    }
}
